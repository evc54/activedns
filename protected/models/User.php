<?php
/**
  Project       : ActiveDNS
  Document      : models/User.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Users model
*/
/**
 * @property integer $id ID
 * @property integer $create Create timestamp
 * @property integer $status Status
 * @property integer $role Role
 * @property string  $realname Real name
 * @property string  $email E-mail
 * @property string  $password Password hash
 * @property integer $isPricingPlan Current pricing plan
 * @property integer $billing Billing cycle
 * @property string  $paidTill Account paid till
 * @property integer $ns1 Assigned nameserver #1
 * @property integer $ns2 Assigned nameserver #2
 * @property integer $ns3 Assigned nameserver #3
 * @property integer $ns4 Assigned nameserver #4
 * @property string  $soaHostmaster Default SOA hostmaster
 * @property integer $soaRefresh Default SOA refresh
 * @property integer $soaRetry Default SOA retry
 * @property integer $soaExpire Default SOA expiry
 * @property integer $soaMinimum Default SOA minimum
 * @property string  $language Language ID
 * @property string  $currency Currency ID
 * @property string  $dateFormat Date format
 * @property string  $timeFormat Time format
 * @property string  $timeZone Time zone
 * @property string  $statisticTimeFormat Statistic time format
 * @property integer $lastEventSeen Last event seen timestamp
 * @property integer $lastSupportSeen Last support messages seen
 * @property integer $lastEventNotifyTime Last time user was notified about events
 * @property integer $expireNotify Expire notifications period parameter
 * @property integer $lastExpireNotifyTime Last time user was notified about domain expirations
 * @property integer $alertNotify Domain alert notifications period parameter
 * @property integer $lastAlertNotifyTime Last time user was notified about domain alerts
 * @property PricingPlan $plan Current pricing plan
 */
class User extends CActiveRecord
{
  const PAGESIZE = 10;

  const USER_DISABLED = 0;
  const USER_ENABLED = 1;

  const ROLE_ADMIN = 1;
  const ROLE_USER = 2;

  const DEFAULT_DOMAINS_MAX = 3;
  const EXPIRING_DOMAINS_FORECAST = 1209600;

  const NOTIFY_ONE_TIME = 10;
  const NOTIFY_EVERY_HOUR = 20;
  const NOTIFY_EVERY_FOUR_HOURS = 30;
  const NOTIFY_EVERY_TWELVE_HOURS = 40;
  const NOTIFY_DAILY = 50;
  const NOTIFY_EVERY_THREE_DAYS = 60;
  const NOTIFY_EVERY_FIVE_DAYS = 70;
  const NOTIFY_WEEKLY = 80;
  const NOTIFY_DISABLED = 999;

  private $salt = 'ad246z';
  public $newPassword;
  public $newPasswordConfirm;

  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{' . __CLASS__ . '}}';
  }

  public function rules()
  {
    return array(
      array('email,status,role', 'required'),
      array('role,status,idPricingPlan,billing,lastEventSeen,lastSupportSeen,ns1,ns2,ns3,ns4,soaRefresh,soaRetry,soaExpire,soaMinimum,expireNotify,alertNotify', 'numerical', 'integerOnly' => true),
      array('paidTill', 'length', 'max' => 30),
      array('language,currency', 'length', 'max' => 16),
      array('realname', 'length', 'max' => 255),
      array('email,timeZone,soaHostmaster', 'length', 'max' => 64),
      array('email', 'unique'),
      array('email', 'email', 'skipOnError' => true),
      array('soaHostmaster', 'email', 'on' => 'manual'),
      array('soaRefresh', 'numerical', 'min' => 7200, 'max' => 86400, 'tooSmall' => Yii::t('error', 'Refresh time can not be less than 2 hours.'), 'tooBig' => Yii::t('error', 'Refresh time can not be more than 24 hours.'), 'on' => 'manual'),
      array('soaRetry', 'numerical', 'min' => 900, 'max' => 86400, 'tooSmall' => Yii::t('error', 'Retry timeout can not be less than 15 minutes.'), 'tooBig' => Yii::t('error', 'Retry timeout can not be more than 24 hours.'), 'on' => 'manual'),
      array('soaExpire', 'numerical', 'min' => 86400, 'max' => 9676800, 'tooSmall' => Yii::t('error', 'Expiry time can not be less than 1 day.'), 'tooBig' => Yii::t('error', 'Expiry time can not be more than 16 weeks.'), 'on' => 'manual'),
      array('soaMinimum', 'numerical', 'min' => 1, 'max' => 604800, 'tooSmall' => Yii::t('error', 'Minimum time-to-live can not be less than 1 second.'), 'tooBig' => Yii::t('error', 'Minimum time-to-live can not be more than 16 weeks.'), 'on' => 'manual'),
      array('password,dateFormat,timeFormat,statisticTimeFormat', 'length', 'max' => 32),
      array('newPassword', 'required', 'on' => 'create'),
      array('newPassword,newPasswordConfirm', 'required', 'on' => 'create,passwordUpdate'),
      array('newPasswordConfirm', 'compare', 'compareAttribute' => 'newPassword', 'on' => 'create,passwordUpdate'),
      array('newPassword', 'length', 'min' => Yii::app()->params['minPasswordLength'], 'encoding' => 'utf-8', 'on' => 'create,passwordUpdate'),
      array('id,status,email', 'safe', 'on' => 'search'),
      array('newPassword,newPasswodConfirm', 'safe'),
    );
  }

  public function relations()
  {
    return array(
      'plan'              => array(self::BELONGS_TO, 'PricingPlan', 'idPricingPlan'),
      'totalDomainsQty'   => array(self::STAT, 'Domain', 'idUser'),
      'activeDomainsQty'  => array(self::STAT, 'Domain', 'idUser', 'condition' => 'status = :active', 'params' => array(':active' => Domain::DOMAIN_HOSTED)),
      'totalAlertsQty'    => array(self::STAT, 'Alert', 'idUser'),
      'totalTemplatesQty' => array(self::STAT, 'Template', 'idUser'),
      'totalEventsQty'    => array(self::STAT, 'DomainEvent', 'idUser'),
    );
  }

  public function assignedNameserverQty()
  {
    return $this->ns4 == 0 && $this->ns3 == 0 ? 2 : 4;
  }

  public function unseenSupportQty()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.replied', '>' . $this->lastSupportSeen);
    $criteria->compare('t.created', '>' . $this->lastSupportSeen, false, 'OR');
    $criteria->compare('t.authorID', $this->id);

    return SupportTicket::model()->count($criteria);
  }

  public function unseenEventsQty()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.create', '>' . $this->lastEventSeen);
    $criteria->compare('t.idUser', $this->id);

    return DomainEvent::model()->count($criteria);
  }

  /**
   * Maximum domains allowed to create
   * @return integer
   */
  public function getMaxDomainsQty()
  {
    if ($this->plan == null) {
      return self::DEFAULT_DOMAINS_MAX;
    }

    return $this->plan->domainsQty;
  }

  public function getExpiringDomains()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.expire', '<=' . date('Y-m-d', time() + self::EXPIRING_DOMAINS_FORECAST));
    $qty = Domain::model()->own()->count($criteria);
    $criteria->limit = 10;
    $criteria->order = 't.expire DESC';
    return array(
      'qty'      => $qty,
      'expiring' => Domain::model()->own()->findAll($criteria),
    );
  }

  public function getStatsKey()
  {
    return 'Stat.Daily.' . $this->id;
  }

  public function getStats()
  {
    $result = Yii::app()->cache->get($this->getStatsKey());
    if ($result == null) {
      $time = time();
      $domains = CHtml::listData(Domain::model()->own()->findAll(), 'id', 'id');

      // blank stats
      $day = array_combine(range(0,23), array_fill(0,24,0));
      $week = array();
      for ($i = 691200; $i >= 86400; $i -= 86400) {
        $week[date('Y-m-d', $time - $i)] = '0';
      }
      $month = array();
      for ($i = 2592000; $i >= 86400; $i -= 86400) {
        $month[date('Y-m-d', $time - $i)] = 0;
      }
      $year = array();
      $monthes = array(
        '',
        Yii::t('statistics', 'Jan'),
        Yii::t('statistics', 'Feb'),
        Yii::t('statistics', 'Mar'),
        Yii::t('statistics', 'Apr'),
        Yii::t('statistics', 'May'),
        Yii::t('statistics', 'Jun'),
        Yii::t('statistics', 'Jul'),
        Yii::t('statistics', 'Aug'),
        Yii::t('statistics', 'Sep'),
        Yii::t('statistics', 'Oct'),
        Yii::t('statistics', 'Nov'),
        Yii::t('statistics', 'Dec'),
      );
      for ($i = 1; $i <= 12; $i++) {
        $year[$monthes[$i]] = 0;
      }

      if (!empty($domains)) {
        $domainsCondition = new CDbCriteria;
        foreach ($domains as $idDomain) {
          $domainsCondition->compare('t.idDomain', $idDomain, false, 'OR');
        }

        // hourly stats for last day
        $criteria = new CDbCriteria;
        $criteria->select = 't.hour,SUM(t.requests) as requests';
        $criteria->compare('t.date',date('Y-m-d', $time - 86400));
        $criteria->mergeWith($domainsCondition);
        $criteria->group = 't.hour';
        $dayStats = DomainStat::model()->findAll($criteria);
        foreach ($dayStats as $stats) {
          $day[$stats->hour] = $stats->requests;
        }

        // daily stats for last week
        $criteria = new CDbCriteria;
        $criteria->select = 't.date,SUM(t.requests) as requests';
        $criteria->addBetweenCondition('t.date', date('Y-m-d', $time - 691200), date('Y-m-d', $time - 86400));
        $criteria->mergeWith($domainsCondition);
        $criteria->group = 't.date';
        $weekStats = DomainStatDaily::model()->findAll($criteria);
        foreach ($weekStats as $stats) {
          $week[$stats->date] = $stats->requests;
        }

        // daily stats for last month
        $criteria = new CDbCriteria;
        $criteria->select = 't.date,SUM(t.requests) as requests';
        $criteria->addBetweenCondition('t.date', date('Y-m-d', $time - 2592000), date('Y-m-d', $time - 86400));
        $criteria->mergeWith($domainsCondition);
        $criteria->group = 't.date';
        $monthStats = DomainStatDaily::model()->findAll($criteria);
        foreach ($monthStats as $stats) {
          $month[$stats->date] = $stats->requests;
        }

        // monthly stats for current year
        $criteria = new CDbCriteria;
        $criteria->select = 'month,SUM(t.requests) as requests';
        $criteria->compare('t.year', date('Y'));
        $criteria->mergeWith($domainsCondition);
        $criteria->group = 'month';
        $yearStats = DomainStatMonthly::model()->findAll($criteria);
        foreach ($yearStats as $stats) {
          $year[$monthes[$stats->month]] = $stats->requests;
        }
      }

      $dayStats = array(
        array(
          Yii::t('statistics', 'Time of a day'),
          Yii::t('statistics', 'Queries'),
        ),
      );
      foreach ($day as $h => $requests) {
        $hour = date($this->statisticTimeFormat ? $this->statisticTimeFormat : 'ha', strtotime(date('Y-m-d ' . $h . ':m:s')));
        $dayStats[] = array(
          $hour,
          (int) $requests,
        );
      }

      $weekStats = array(
        array(
          Yii::t('statistics', 'Day of week'),
          Yii::t('statistics', 'Queries'),
        ),
      );
      foreach ($week as $date => $requests) {
        $weekStats[] = array(
          $this->dateFormat ? date($this->dateFormat, strtotime($date)) : $date,
          (int) $requests,
        );
      }

      $monthStats = array(
        array(
          Yii::t('statistics', 'Date'),
          Yii::t('statistics', 'Queries'),
        ),
      );
      foreach ($month as $date => $requests) {
        $monthStats[] = array(
          $this->dateFormat ? date($this->dateFormat, strtotime($date)) : $date,
          (int) $requests,
        );
      }

      $yearStats = array(
        array(
          Yii::t('statistics', 'Date'),
          Yii::t('statistics', 'Queries'),
        ),
      );
      foreach ($year as $date => $requests) {
        $yearStats[] = array(
          $date,
          (int) $requests,
        );
      }

      $result = array(
        'day'   => $dayStats,
        'week'  => $weekStats,
        'month' => $monthStats,
        'year'  => $yearStats,
      );

      Yii::app()->cache->set($this->getStatsKey(), $result, intval(strtotime(date('Y-m-d 23:59:59')) - $time));
    }

    return $result;
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'email' => Yii::t('user', 'E-mail'),
      'role' => Yii::t('user', 'Role'),
      'status' => Yii::t('user', 'Status'),
      'idPricingPlan' => Yii::t('user', 'Pricing plan'),
      'billing' => Yii::t('user', 'Billing cycle'),
      'paidTill' => Yii::t('user', 'Paid till'),
      'realname' => Yii::t('user', 'Real name'),
      'password' => Yii::t('user', 'Current password'),
      'newPassword' => Yii::t('user', 'New Password'),
      'newPasswordConfirm' => Yii::t('user', 'Confirm new password'),
      'language' => Yii::t('user', 'Language'),
      'currency' => Yii::t('user', 'Currency'),
      'dateFormat' => Yii::t('user', 'Date format'),
      'timeFormat' => Yii::t('user', 'Time format'),
      'timeZone' => Yii::t('user', 'Time zone'),
      'statisticTimeFormat' => Yii::t('user', 'Statistics time format'),
      'ns1' => Yii::t('user', 'Assigned master nameserver'),
      'ns2' => Yii::t('user', 'Assigned slave nameserver #1'),
      'ns3' => Yii::t('user', 'Assigned slave nameserver #2'),
      'ns4' => Yii::t('user', 'Assigned slave nameserver #3'),
      'soaHostmaster' => Yii::t('domain', 'Hostmaster'),
      'soaRefresh' => Yii::t('domain', 'Refresh'),
      'soaRetry' => Yii::t('domain', 'Retry'),
      'soaExpire' => Yii::t('domain', 'Expiry'),
      'soaMinimum' => Yii::t('domain', 'Minimum'),
      'expireNotify' => Yii::t('user', 'Expiring domains notifications'),
      'alertNotify' => Yii::t('user', 'Domain\'s alerts notifications'),
    );
  }

  public function attributeLabelsRole()
  {
    return array(
      self::ROLE_USER  => Yii::t('user', 'Customer'),
      self::ROLE_ADMIN => Yii::t('user', 'Administrator'),
    );
  }

  public function attributeLabelsStatus()
  {
    return array(
      self::USER_ENABLED  => Yii::t('user', 'Enabled'),
      self::USER_DISABLED => Yii::t('user', 'Disabled'),
    );
  }

  public function getAttributeLabelStatus($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }

    $labels = $this->attributeLabelsStatus();

    return isset($labels[$status]) ? $labels[$status] : '';
  }

  public function getStatusClass($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }

    $classes = array(
      self::USER_DISABLED => 'disabled',
      self::USER_ENABLED  => 'enabled',
    );

    return (isset($classes[$status])) ? $classes[$status] : '';
  }

  public function disable()
  {
    $this->status = self::USER_DISABLED;
    return $this->save();
  }

  public function enable()
  {
    $this->status = self::USER_ENABLED;
    return $this->save();
  }

  public function setPassword($password)
  {
    $this->password=$this->hashPassword($password, $this->salt);
    return $this->save();
  }

  public function generatePassword($_length)
  {
    $_chars = '$0123456789_ABCDEFGHIJKLMNOPQRSTUVWXYZ-abcdefghijklmnopqrstuvwxyz%';
    $_retValue = "";
    for ($_i = 0; $_i < $_length; $_i++)
        $_retValue .= $_chars[rand(0, strlen($_chars) - 1)];
    return $_retValue;
  }

  public function validatePassword($password)
  {
    return $this->hashPassword($password,$this->salt) === $this->password;
  }

  public function hashPassword($password, $salt)
  {
    return md5($salt . $password);
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if (($this->scenario == 'passwordUpdate') || ($this->scenario == 'create')) {
        $this->password = $this->hashPassword($this->newPassword, $this->salt);
      }

      if ($this->isNewRecord) {
        $this->create = time();
      }

      $this->soaHostmaster = str_replace('@', '.', trim($this->soaHostmaster, ' .')) . '.';
      $this->soaMinimum = $this->plan ? max($this->plan->minTtl, $this->soaMinimum) : max($this->soaMinimum, ResourceRecord::DEFAULT_TTL);

      return true;
    }

    return false;
  }

  public function beforeDelete()
  {
    if (parent::beforeDelete()) {

      Alert::model()->deleteAllByAttributes(array('idUser' => $this->id));
      DomainEvent::model()->deleteAllByAttributes(array('idUser' => $this->id));

      return true;
    }

    return false;
  }

  public function search()
  {
    $sort = new CSort;
    $sort->attributes = array(
      'id',
      'status',
      'email',
      'idPricingPlan',
      'paidTill',
      'totalDomainsQty' => array(
        'asc' => 'totalDomainsQty ASC',
        'desc' => 'totalDomainsQty DESC',
      ),
    );
    $sort->defaultOrder = 't.create DESC';

    $criteria = new CDbCriteria;
    $criteria->with[] = 'plan';
    $criteria->with[] = 'totalDomainsQty';
    $criteria->join = 'LEFT OUTER JOIN (SELECT d.idUser AS idUser,COUNT(*) AS totalDomainsQty FROM {{Domain}} d GROUP BY idUser) d ON (d.idUser = t.id)';
    $criteria->compare('id', $this->id, true);
    $criteria->compare('email', $this->email, true);
    $criteria->compare('role', $this->role, false);
    $criteria->compare('status', $this->status, false);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => $sort,
    ));
  }
}
