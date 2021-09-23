<?php
/**
  Project       : ActiveDNS
  Document      : models/Domain.php
  Document type : PHP script file
  Created at    : 06.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domains model
*/
/**
 * @property integer $id Domain ID
 * @property integer $idUser Domain owner
 * @property integer $idZoneCurrent Current zone ID
 * @property integer $idZoneReplicated Last replicated zone ID
 * @property string  $status Domain status
 * @property string  $name Domain name
 * @property integer $create Create time
 * @property integer $update Last update time
 * @property integer $lastAutoCheck Last automated check time by whois module
 * @property boolean $allowAutoCheck Allow domain info automatic checking
 * @property string  $ns1 First nameserver assigned to domain
 * @property string  $ns2 Second nameserver assigned to domain
 * @property string  $ns3 Third nameserver assigned to domain
 * @property string  $ns4 Fourth nameserver assigned to domain
 * @property string  $register Domain registration date
 * @property string  $renewal Domain last update
 * @property string  $expire Domain expiration date
 * @property string  $registrar Domain registrar
 * @property User    $owner Domain owner
 * @property Zone    $zone Domain zones
 * @property Zone    $currentZone Domain current (active) zone
 */
class Domain extends CActiveRecord
{
  /**
   * Elements on page
   */
  const PAGESIZE = 10;

  /**
   * Domain just created and waiting for check by whois
   */
  const DOMAIN_WAITING = 1;

  /**
   * Domain successfully hosted (current nameservers complies assigned)
   */
  const DOMAIN_HOSTED = 2;

  /**
   * Domain expired and not hosted on NS's
   */
  const DOMAIN_EXPIRED = 3;

  /**
   * Domain disabled and not hosted on NS's
   */
  const DOMAIN_DISABLED = 4;

  /**
   * Domain in queue to be removed from NS's
   */
  const DOMAIN_REMOVE = 5;

  /**
   * Domain in queue to be promoted into NS's
   */
  const DOMAIN_UPDATE = 6;

  /**
   * Domain in alert state
   */
  const DOMAIN_ALERT = 255;

  /**
   * Order domains by alphabet
   */
  const ORDER_ALPHABET = 'name ASC';

  /**
   * Order domains by id
   */
  const ORDER_ID = 'id DESC';

  /**
   * @var date Expiration date for grid filtering and sorting
   */
  public $expire;

  /**
   * @var datetime Alert appearing date for grid filtering and sorting
   */
  public $appeared;

  /**
   * @var boolean Domain is read-only
   */
  public $readonly = false;

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
      array('idUser,name', 'required'),
      array('name', 'length', 'max'=>255),
      array('name', 'unique'),
      array('idUser,idZoneCurrent,idZoneReplicated,create,update,lastAutoCheck', 'numerical', 'integerOnly' => true),
      array('status', 'in', 'range' => self::attributeStatus()),
      array('expire,appeared,readonly', 'unsafe'),
      array('ns1,ns2,ns3,ns4', 'length', 'max' => 127, 'allowEmpty' => true),
      array('register,renewal,expire', 'length', 'max' => 10),
      array('registrar', 'length', 'max' => 255),
      array('allowAutoCheck', 'boolean'),
      array('id,idUser,status,name,expire', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'owner'       => array(self::BELONGS_TO, 'User', 'idUser'),
      'zone'        => array(self::HAS_MANY, 'Zone', 'idDomain','order' => 'zone.id DESC', 'limit' => 10),
      'currentZone' => array(self::BELONGS_TO, 'Zone', 'idZoneCurrent'),
      'newZone'     => array(self::HAS_ONE, 'Zone', 'idDomain', 'condition' => 'serial=0'),
      'alerts'      => array(self::HAS_MANY, 'Alert', 'idDomain'),
      'lastAlert'   => array(self::HAS_ONE, 'Alert', 'idDomain', 'order' => 'lastAlert.id DESC'),
      'lastStat'    => array(self::HAS_MANY, 'DomainStat', 'idDomain', 'condition' => Yii::app()->db->quoteColumnName('lastStat.date') . '=DATE(DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY))'),
      'events'      => array(self::HAS_MANY, 'DomainEvent', 'idDomain'),
      'lastEvent'   => array(self::HAS_ONE, 'DomainEvent', 'idDomain', 'order' => 'lastEvent.id DESC'),
      'transfer'    => array(self::HAS_MANY, 'DomainTransfer', 'domainID'),
    );
  }

  public function own()
  {
    if (Yii::app()->user->getRole() != User::ROLE_ADMIN) {
      $this->getDbCriteria()->mergeWith(array(
        'condition' => 't.idUser = :idUser',
        'params' => array(
          ':idUser' => Yii::app()->user->id,
        ),
      ));
    }

    return $this;
  }

  public function orderBy($order)
  {
    $this->getDbCriteria()->mergeWith(array(
      'order' => $order,
    ));

    return $this;
  }

  public function enabled()
  {
    $criteria = new CDbCriteria;
    $criteria->addInCondition('t.status', array(
      self::DOMAIN_HOSTED,
      self::DOMAIN_EXPIRED,
      self::DOMAIN_ALERT,
      self::DOMAIN_UPDATE,
    ));

    $this->getDbCriteria()->mergeWith($criteria);

    return $this;
  }

  public function active()
  {
    $criteria = new CDbCriteria;
    $criteria->addInCondition('t.status', array(
      self::DOMAIN_HOSTED,
    ));

    $this->getDbCriteria()->mergeWith($criteria);

    return $this;
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'idUser' => Yii::t('domain', 'Owner'),
      'name' => Yii::t('domain', 'Domain name'),
      'status' => Yii::t('domain', 'Status'),
      'create' => Yii::t('domain', 'Created at'),
      'update' => Yii::t('domain', 'Last update at'),
      'expire' => Yii::t('domain', 'Expires at'),
      'allowAutoCheck' => Yii::t('domain', 'Info autochecking allowed'),
    );
  }

  public static function attributeStatus()
  {
    return array(
      self::DOMAIN_WAITING,
      self::DOMAIN_HOSTED,
      self::DOMAIN_EXPIRED,
      self::DOMAIN_DISABLED,
      self::DOMAIN_REMOVE,
      self::DOMAIN_UPDATE,
      self::DOMAIN_ALERT,
    );
  }

  public function attributeStatusLabels()
  {
    return array_combine(
      self::attributeStatus(),
      array(
        Yii::t('domainStatus', 'Waiting'),
        Yii::t('domainStatus', 'Active'),
        Yii::t('domainStatus', 'Expired'),
        Yii::t('domainStatus', 'Disabled'),
        Yii::t('domainStatus', 'Removing'),
        Yii::t('domainStatus', 'Updating'),
        Yii::t('domainStatus', 'Alert'),
      )
    );
  }

  public function getAttributeStatusLabel($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }
    $labels = $this->attributeStatusLabels();

    return isset($labels[$status]) ? $labels[$status] : '';
  }

  public function attributeStatusClass()
  {
    return array_combine(
      self::attributeStatus(),
      array(
        'success',
        'success',
        'warning',
        'inverse',
        'important',
        'info',
        'warning',
      )
    );
  }

  public function getAttributeStatusClass($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }
    $labels = $this->attributeStatusClass();

    return isset($labels[$status]) ? $labels[$status] : '';
  }

  public function attributeStatusHint($status = null)
  {
    return array(
      self::DOMAIN_ALERT    => $this->lastAlert ? Yii::t('alerts', $this->lastAlert->alert) : Yii::t('domainStatusHint', 'Unknown alert'),
      self::DOMAIN_HOSTED   => Yii::t('domainStatusHint', 'Domain is operational'),
      self::DOMAIN_EXPIRED  => Yii::t('domainStatusHint', 'Domain has been expired'),
      self::DOMAIN_DISABLED => Yii::t('domainStatusHint', 'Domain disabled (not operational)'),
      self::DOMAIN_REMOVE   => Yii::t('domainStatusHint', 'Domain in removal process'),
      self::DOMAIN_UPDATE   => Yii::t('domainStatusHint', "Domain's zone update replicating to the nameservers"),
      self::DOMAIN_WAITING  => Yii::t('domainStatusHint', 'Domain is just added and waiting for check'),
    );
  }

  public function getAttributeStatusHint($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }
    $labels = $this->attributeStatusHint();

    return isset($labels[$status]) ? $labels[$status] : '';
  }

  public function getDomainNameservers($zone = null)
  {
    $ns = array();
    if ($zone == null) {
      $zone = $this->currentZone;
    }

    if (!empty($zone)) {
      if (!empty($zone->nameserverAlias)) {
        $alias = $zone->nameserverAlias;
        $ns[$alias->idNameServerMaster] = $alias->NameServerMasterAlias;
        $ns[$alias->idNameServerSlave1] = $alias->NameServerSlave1Alias;
        if (!empty($alias->NameServerSlave2Alias) && !empty($alias->NameServerSlave3Alias)) {
          $ns[$alias->idNameServerSlave2] = $alias->NameServerSlave2Alias;
          $ns[$alias->idNameServerSlave3] = $alias->NameServerSlave3Alias;
        }
      }
      else {
        foreach ($zone->nameservers as $nameserver) {
          $ns[$nameserver->id] = $nameserver->name;
        }
      }
    }

    return $ns;
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN) {
      $criteria->with = array(
        'owner',
      );
      $criteria->compare('owner.id', $this->idUser, true);
      $criteria->compare('owner.email', $this->idUser, true, 'OR');
    }
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.name', $this->name, true);
    $criteria->compare('t.status', $this->status, false);

    $pageSize = Yii::app()->user->getState('DomainsPerPage', self::PAGESIZE);
    if ($pageSize == 'all') {
      $pagination = false;
    }
    else {
      $pagination = array('pageSize' => $pageSize);
    }

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => $pagination,
      'sort' => array(
        'defaultOrder' => 't.name ASC',
      ),
    ));
  }

  public function transfers()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.domainID' ,$this->id);

    return new CActiveDataProvider('DomainTransfer', array(
      'criteria' => $criteria,
      'pagination' => false,
      'sort' => array(
        'defaultOrder' => 't.address ASC',
      ),
    ));
  }

  public function alerts()
  {
    $sort = new CSort;
    $sort->attributes = array(
      'idUser',
      'name',
      'status',
      'appeared' => array(
        'asc' => 'lastAlert.create ASC',
        'desc' => 'lastAlert.create DESC',
      ),
    );
    $sort->defaultOrder = 'lastAlert.create DESC';

    $criteria = new CDbCriteria;
    $criteria->with = 'lastAlert';
    $criteria->compare('lastAlert.id','>0');
    if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
      $criteria->compare('t.idUser',Yii::app()->user->id);
    }
    $criteria->addInCondition('t.status', array(self::DOMAIN_WAITING, self::DOMAIN_UPDATE, self::DOMAIN_ALERT, self::DOMAIN_EXPIRED));

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => $sort,
    ));
  }

  public function expiring()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.expire','<=' . date('Y-m-d', time() + User::EXPIRING_DOMAINS_FORECAST));
    if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
      $criteria->compare('t.idUser', Yii::app()->user->id);
    }
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.name', $this->name,true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize'=>self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.expire') . ' DESC',
      ),
    ));
  }

  public function afterFind()
  {
    parent::afterFind();

    if (in_array($this->status,array(self::DOMAIN_REMOVE))) {
      $this->readonly = true;
    }
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {

      if ($this->isNewRecord) {
        $this->name = strtoupper($this->name);
        $this->create = time();
      }

      return true;
    }

    return false;
  }

  public function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if ($this->zone) {
        foreach ($this->zone as $zone) {
          $zone->delete();
        }
      }

      Alert::model()->deleteAllByAttributes(array('idDomain' => $this->id));
      DomainEvent::model()->deleteAllByAttributes(array('idDomain' => $this->id));
      DomainStat::model()->deleteAllByAttributes(array('idDomain' => $this->id));

      return true;
    }

    return false;
  }

  public function alert($type, $message)
  {
    Alert::model()->deleteAllByAttributes(array('idDomain' => $this->id));

    $alert = new Alert;
    $alert->idUser = $this->idUser;
    $alert->idDomain = $this->id;
    $alert->type = $type;
    $alert->alert = mb_strlen($message,Yii::app()->charset) > 1022 ? mb_strcut($message,0,1022,Yii::app()->charset) : $message;
    $alert->save();

    $this->status = self::DOMAIN_ALERT;
    $this->save();
  }

  public function getPointsForCname()
  {
    if ($this->isNewRecord) {
      return array();
    }

    $criteria = new CDbCriteria;
    $criteria->compare('idZone', $this->idZoneCurrent);
    $criteria->compare('type', ResourceRecord::TYPE_A);
    $criteria->compare('type', ResourceRecord::TYPE_AAAA, false, 'OR');

    return CHtml::listData(ResourceRecord::model()->findAll($criteria), 'host', 'host');
  }

  public function enable()
  {
    $this->status = self::DOMAIN_WAITING;
    $this->lastAutoCheck = 0;
    $this->idZoneReplicated = 0;

    return $this->save();
  }

  public function ok()
  {
    $this->status = self::DOMAIN_HOSTED;
    $this->clearAlerts();

    return $this->save();
  }

  public function disable()
  {
    $this->status = self::DOMAIN_DISABLED;
    $this->update = time();

    return $this->save();
  }

  public function remove()
  {
    $this->status = self::DOMAIN_REMOVE;
    $this->update = time();

    return $this->save();
  }

  public function changeNS($idNameServerAlias, $apply = false, $zone = null)
  {
    $transaction = Yii::app()->db->beginTransaction();

    if ($zone == null) {
      if (!empty($this->newZone)) {
        $zone = $this->newZone;
      }
      elseif (!empty($this->currentZone)) {
        $zone = $this->currentZone;
      }
    }

    if (empty($zone)) {
      $zone = $this->makeNewZone();
    }
    elseif ($zone->serial) {
      $skipNSRecords = CHtml::listData(ResourceRecord::model()->findAllByAttributes(array(
        'type' => ResourceRecord::TYPE_NS,
        'readonly' => true,
        'idZone' => $zone->id,
      )), 'id', 'id');
      $zone = $this->makeNewZone($zone, $skipNSRecords);
    }
    else {
      ResourceRecord::model()->deleteAllByAttributes(array(
        'type' => ResourceRecord::TYPE_NS,
        'readonly' => true,
        'idZone' => $zone->id,
      ));
    }

    if (empty($idNameServerAlias)) {
      $zone->idNameServerAlias = 0;
    }
    else {
      $zone->idNameServerAlias = $idNameServerAlias;
      $alias = NameServerAlias::model()->findByPk($idNameServerAlias);
      if ($alias instanceof NameServerAlias) {
        $nameservers = $alias->getNameservers();
      }
    }
    if (empty($nameservers)) {
      $nameservers = $this->owner->plan->getNameservers();
    }
    $zone->save();

    ZoneNameServer::model()->deleteAllByAttributes(array('zoneID' => $zone->id));

    $eventParams = array();
    foreach ($nameservers as $nameserver) {
      $nsBind = new ZoneNameServer;
      $nsBind->zoneID = $zone->id;
      $nsBind->nameServerID = $nameserver->id;
      if (!$nsBind->save()) {
        file_put_contents(Yii::app()->runtimePath . DIRECTORY_SEPARATOR . 'changes.log', print_r($nsBind->getErrors(), true), FILE_APPEND);
      }

      if (!empty($alias)) {
        foreach (array(
          'NameServerMasterAlias' => 'idNameServerMaster',
          'NameServerSlave1Alias' => 'idNameServerSlave1',
          'NameServerSlave2Alias' => 'idNameServerSlave2',
          'NameServerSlave3Alias' => 'idNameServerSlave3',
        ) as $aliasName => $nameserverID) {
          if ($nameserver->id == $alias->getAttribute($nameserverID)) {
            $host = $alias->getAttribute($aliasName);
            break;
          }
        }
      }
      else {
        $host = $nameserver->name;
      }

      $nsRecord = new ResourceRecord;
      $nsRecord->idZone = $zone->id;
      $nsRecord->host = '@';
      $nsRecord->class = ResourceRecord::DEFAULT_CLASS;
      $nsRecord->type = ResourceRecord::TYPE_NS;
      $nsRecord->rdata = $host . '.';
      $nsRecord->ttl = ResourceRecord::DEFAULT_TTL;
      $nsRecord->readonly = true;
      $nsRecord->save();

      $eventParams[] = $host;
    }

    if ($apply) {
      $this->applyZone($zone);
    }
    Yii::app()->user->setState($this->id . '.current.zone', $zone->id);

    $this->logEvent(DomainEvent::TYPE_DOMAIN_CHANGE_NAMESERVERS,array('{nameservers}' => implode(', ', $eventParams)));

    $transaction->commit();

    return true;
  }

  public function replicate()
  {
    if ($this->status != self::DOMAIN_DISABLED) {
      $this->status = self::DOMAIN_UPDATE;
      $this->update = time();
    }

    return $this->save();
  }

  public function template($templateID, $zone = null, $priority = Template::PRIORITY_ZONE, $apply = false)
  {
    $template = Template::model()->findByPk($templateID);
    if ($template == null) {
      return false;
    }

    if ($zone == null) {
      $zone = empty($this->newZone) ? $this->makeNewZone($this->currentZone) : $this->newZone;
    }

    $add = array();
    foreach ($template->records as $record) {
      $records = ResourceRecord::model()->findAllByAttributes(array(
        'idZone'   => $zone->id,
        'host'     => $record->host,
        'class'    => $record->class,
        'type'     => $record->type,
        'priority' => $record->priority,
        'proto'    => $record->proto,
        'weight'   => $record->weight,
        'port'     => $record->port,
      ));

      if ($priority == Template::PRIORITY_ZONE && $records !== null) {
        continue;
      }
      else {
        if ($records !== null) {
          foreach ($records as $rr) {
            if (!$rr->readonly) {
              $rr->delete();
            }
          }
        }
        $add[] = array(
          'idZone'   => $zone->id,
          'host'     => $record->host,
          'class'    => $record->class,
          'type'     => $record->type,
          'rdata'    => $record->rdata,
          'ttl'      => $record->ttl,
          'priority' => $record->priority,
          'proto'    => $record->proto,
          'name'     => $record->name,
          'weight'   => $record->weight,
          'port'     => $record->port,
          'target'   => $record->target,
        );
      }
    }

    foreach ($add as $record) {
      $rr = new ResourceRecord;
      $rr->setAttributes($record,false);
      $rr->save();
    }

    if ($apply) {
      $this->applyZone($zone);
    }

    $this->newZone = $zone;

    return true;
  }

  /**
   * Makes new zone using source zone as template
   *
   * @param Zone $sourceZone source zone
   * @param array $skipRecord IDs for records need to be skipped
   * @return Zone new zone
   */
  public function makeNewZone($sourceZone = null, $skipRecord = array())
  {
    $newZone = new Zone;
    $newZone->idDomain = $this->id;
    $newZone->save();

    if ($sourceZone != null) {
      $newZone->attributes = $sourceZone->attributes;
      $newZone->serial = 0;
      if ($sourceZone->record) {
        foreach ($sourceZone->record as $record) {
          if (in_array($record->id,$skipRecord)) {
            continue;
          }
          $newRecord = new ResourceRecord;
          $newRecord->attributes = $record->attributes;
          $newRecord->idZone = $newZone->id;
          $newRecord->save();
        }
      }
      if ($sourceZone->nameservers) {
        foreach ($sourceZone->nameservers as $nameserver) {
          $newNS = new ZoneNameServer;
          $newNS->zoneID = $newZone->id;
          $newNS->nameServerID = $nameserver->id;
          $newNS->save();
        }
      }
      $newZone->save();
    }

    return $newZone;
  }

  public function applyZone($zone)
  {
    if ($zone->serial == 0) {
      $zone->serial = $this->getNextSerial();
      if ($zone->save()) {
        $this->idZoneCurrent = $zone->id;
        $this->replicate();
        $this->logEvent(DomainEvent::TYPE_DOMAIN_CHANGE_ZONE,array('{serial}' => $zone->serial));

        return true;
      }
    }

    return false;
  }

  public function getDomainName($encode = true)
  {
    if ($encode) {
      if (!Yii::getPathOfAlias('Iodev')) {
        Yii::setPathOfAlias('Iodev', Yii::getPathOfAlias('application.vendor.Iodev'));
      }

      $name = \Iodev\Whois\Helpers\DomainHelper::toUnicode($this->name);
    }
    else {
      $name = $this->name;
    }

    return mb_convert_case($name, MB_CASE_LOWER, Yii::app()->charset);
  }

  public function createZoneFiles()
  {
    $success = true;

    $zoneConfig = array();
    if ($this->currentZone) {
      foreach ($this->currentZone->nameservers as $nameserver) {
        $zoneConfig[] = array(
          'name'        => $this->getDomainName(),
          'zoneDir'     => $nameserver->getZoneDir(),
          'zoneFileDir' => $nameserver->getZoneFileDir(),
          'content'     => $nameserver->generateZoneConfigFile($this),
        );
      }

      $zoneRRFile = $this->currentZone->generateZoneRRFile();

      foreach ($zoneConfig as $config) {
        file_put_contents($config['zoneDir'] . DIRECTORY_SEPARATOR . $config['name'], $config['content']);
        file_put_contents($config['zoneFileDir'] . DIRECTORY_SEPARATOR . $config['name'], $zoneRRFile);
      }
    }
    else {
      $success = false;
    }

    return $success;
  }

  public function unlinkZoneFiles()
  {
    $success = true;

    if (!empty($this->currentZone->nameservers)) {
      foreach ($this->currentZone->nameservers as $nameserver) {
        $dir = $nameserver->getDir();
        foreach (array(
          'zone.conf.d',
          'zonefile.conf.d'
        ) as $zoneDir) {
          $file = $dir . DIRECTORY_SEPARATOR . $zoneDir . DIRECTORY_SEPARATOR . $this->getDomainName();
          if (file_exists($file)) {
            $success &= is_writable($file) && unlink($file);
          }
        }
      }
    }

    return $success;
  }

  public function renderStats()
  {
    $stat = array_combine(range(0, 23), array_fill(0, 24, 0));
    foreach ($this->lastStat as $hour) {
      $stat[$hour->hour] = $hour->requests;
    }

    return CHtml::tag('div', array('class' => 'sparklines'),'<!-- ' . implode(',', array_values($stat)) . ' -->');
  }

  public function setNameServers($nameservers = array())
  {
    asort($nameservers);
    $nameserverList = array_values($nameservers);
    $nameserverAttributes = array('ns1', 'ns2', 'ns3', 'ns4');
    foreach ($nameserverAttributes as $key => $attribute) {
      $this->setAttribute($attribute, empty($nameserverList[$key]) ? '' : $nameserverList[$key]);
    }
  }

  public function emptyDomainInfo()
  {
    if (empty($this->ns1) || empty($this->ns2)) {
      return true;
    }

    if ($this->register == null || $this->expire == null) {
      return true;
    }

    if (empty($this->registrar)) {
      return true;
    }

    return false;
  }

  public function clearAlerts()
  {
    Alert::model()->deleteAllByAttributes(array('idDomain'=>$this->id));
  }

  public function getNextSerial()
  {
    $counter = 0;
    do {
      $counter++;
      $serial = date('Ymd') . sprintf('%02d', $counter);
    } while (Zone::model()->countByAttributes(array('idDomain' => $this->id, 'serial' => $serial)));

    return $serial;
  }

  public function getCommonServicesList()
  {
    return array(
      '',
      'www',
      'ftp',
      'mail',
    );
  }

  public function logEvent($event,$params = array())
  {
    $log = new DomainEvent;
    $log->idUser = Yii::app()->hasComponent('user') ? Yii::app()->user->id : $this->idUser;
    $log->idDomain = $this->id;
    $log->idEventType = $event;
    $log->setEventMessage($params);

    return $log->save();
  }

  public function isExpired()
  {
    $time = time();
    $expire = strtotime($this->expire . ' 00:00:00');

    return $time > $expire;
  }

  public function hasExpireDate()
  {
    return $this->expire != null;
  }

  public function getLevel()
  {
    $name = explode('.', $this->getDomainName());

    return count($name);
  }
}
