<?php
/**
  Project       : ActiveDNS
  Document      : models/DomainEvent.php
  Document type : PHP script file
  Created at    : 06.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain events model
*/
/**
 * @property integer $idUser
 * @property integer $idDomain
 * @property integer $idEventType
 * @property string  $create
 * @property string  $event
 * @property string  $param
 */
class DomainEvent extends CActiveRecord
{
  const TYPE_DOMAIN_CREATED = 10;
  const TYPE_DOMAIN_UPDATED = 20;
  const TYPE_DOMAIN_CHANGE_NAMESERVERS = 25;
  const TYPE_DOMAIN_CHANGE_ZONE = 30;
  const TYPE_DOMAIN_EXPIRED = 40;
  const TYPE_DOMAIN_DISABLED = 50;
  const TYPE_DOMAIN_ENABLED = 60;
  const TYPE_DOMAIN_REMOVING = 70;
  const TYPE_DOMAIN_REMOVED = 80;

  const PAGESIZE = 30;
  const INDEX_PAGESIZE = 10;

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
      array('idUser,idDomain,idEventType,event', 'required'),
      array('idUser,idDomain,idEventType,create', 'numerical', 'integerOnly' => true),
      array('name', 'length', 'max' => 255),
      array('event,param', 'length', 'max' => 1022),
      array('idDomain,idEventType,name', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'domain' => array(self::BELONGS_TO, 'Domain', 'idDomain'),
      'owner' => array(self::BELONGS_TO, 'User', 'idUser'),
    );
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->create = time();
        $this->name = mb_convert_case($this->domain->getDomainName(false), MB_CASE_UPPER, Yii::app()->charset);
      }

      return true;
    }

    return false;
  }

  public function attributeLabels()
  {
    return array(
      'idUser'      => Yii::t('events', 'Owner'),
      'idDomain'    => Yii::t('events', 'Domain'),
      'idEventType' => Yii::t('events', 'Type'),
      'name'        => Yii::t('events', 'Domain name'),
      'create'      => Yii::t('events', 'Appeared'),
      'event'       => Yii::t('events', 'Event'),
    );
  }

  public function attributeTypeLabels()
  {
    return array(
      self::TYPE_DOMAIN_CREATED            => Yii::t('events', 'Created'),
      self::TYPE_DOMAIN_UPDATED            => Yii::t('events', 'Replicated'),
      self::TYPE_DOMAIN_CHANGE_NAMESERVERS => Yii::t('events', 'NS changed'),
      self::TYPE_DOMAIN_CHANGE_ZONE        => Yii::t('events', 'Zone changed'),
      self::TYPE_DOMAIN_EXPIRED            => Yii::t('events', 'Expired'),
      self::TYPE_DOMAIN_ENABLED            => Yii::t('events', 'Enabled'),
      self::TYPE_DOMAIN_DISABLED           => Yii::t('events', 'Disabled'),
      self::TYPE_DOMAIN_REMOVING           => Yii::t('events', 'Removing'),
      self::TYPE_DOMAIN_REMOVED            => Yii::t('events', 'Removed'),
    );
  }

  public function attributeTypeClass()
  {
    return array(
      self::TYPE_DOMAIN_CREATED            => 'success',
      self::TYPE_DOMAIN_UPDATED            => 'info',
      self::TYPE_DOMAIN_CHANGE_NAMESERVERS => 'info',
      self::TYPE_DOMAIN_CHANGE_ZONE        => 'info',
      self::TYPE_DOMAIN_EXPIRED            => 'warning',
      self::TYPE_DOMAIN_ENABLED            => 'success',
      self::TYPE_DOMAIN_DISABLED           => 'inverse',
      self::TYPE_DOMAIN_REMOVING           => 'important',
      self::TYPE_DOMAIN_REMOVED            => 'important',
    );
  }

  public function getAttributeTypeLabel($type = null)
  {
    if ($type === null) {
      $type = $this->idEventType;
    }
    $labels = $this->attributeTypeLabels();

    return isset($labels[$type]) ? $labels[$type] : '';
  }

  public function getAttributeTypeClass($type = null)
  {
    if ($type === null) {
      $type = $this->idEventType;
    }
    $classes = $this->attributeTypeClass();

    return isset($classes[$type]) ? $classes[$type] : '';
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
      $criteria->compare('t.idUser', Yii::app()->user->id);
    }
    else {
      $criteria->with = array(
        'domain',
        'owner',
      );
      $criteria->compare('owner.id', $this->idUser);
      $criteria->compare('owner.email', $this->idUser, true, 'OR');
    }
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.idDomain', $this->idDomain);
    $criteria->compare('t.idEventType', $this->idEventType);
    $criteria->compare('t.event', $this->event, true);
    $criteria->compare('t.name' , $this->name, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.create') . ' DESC',
      ),
    ));
  }

  public function index()
  {
    $criteria = new CDbCriteria;
    if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN) {
      $criteria->with = array(
        'owner',
      );
    }
    else {
      $criteria->compare('t.idUser', Yii::app()->user->id);
    }
    $criteria->order = 't.create DESC';
    $criteria->limit = self::INDEX_PAGESIZE;

    return self::model()->findAll($criteria);
  }

  public function getParam()
  {
    $param = @unserialize($this->param);
    return is_array($param) ? $param : array();
  }

  public function setEventMessage($params)
  {
    $this->event = $this->getEventMessage($this->idEventType);
    $this->param = serialize($params);
  }

  public function getEventMessage($event)
  {
    switch ($event) {
      case self::TYPE_DOMAIN_CREATED:
        return 'Domain just added and waiting to be replicated';
      case self::TYPE_DOMAIN_UPDATED:
        return 'Domain zone version {serial} successfully replicated to nameservers';
      case self::TYPE_DOMAIN_CHANGE_NAMESERVERS:
        return 'Domain nameservers were changed to "{nameservers}"';
      case self::TYPE_DOMAIN_CHANGE_ZONE:
        return 'Domain zone changed to new version {serial}';
      case self::TYPE_DOMAIN_EXPIRED:
        return 'Domain has been expired';
      case self::TYPE_DOMAIN_DISABLED:
        return 'Domain has beed disabled and it\'s zone removed from nameservers';
      case self::TYPE_DOMAIN_ENABLED:
        return 'Domain has been enabled and waiting to be replicated';
      case self::TYPE_DOMAIN_REMOVING:
        return 'Domain asked to be removed';
      case self::TYPE_DOMAIN_REMOVED:
        return 'Domain has been removed';
    }
  }
}
