<?php
/**
  Project       : ActiveDNS
  Document      : models/ResourceRecord.php
  Document type : PHP script file
  Created at    : 06.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain zone's resource records model
*/
class ResourceRecord extends CActiveRecord
{
  /**
   * Default resource record class - IN (internet)
   */
  const DEFAULT_CLASS = 'IN';
  /**
   * Default resource record type - A (IPv4 address)
   */
  const DEFAULT_TYPE = 'A';
  /**
   * Zone Start of Authority it is not a resource record
   * Held here only for further compliance
   */
  const TYPE_SOA = 'SOA';
  /**
   * Resource record type A (IPv4 address)
   */
  const TYPE_A = 'A';
  /**
   * Resource record type AAAA (IPv6 address)
   */
  const TYPE_AAAA = 'AAAA';
  /**
   * Resource record type MX (mail exchange)
   */
  const TYPE_MX = 'MX';
  /**
   * Resource record type NS (name server)
   */
  const TYPE_NS = 'NS';
  /**
   * Resource record type CNAME (canonical name for alias)
   */
  const TYPE_CNAME = 'CNAME';
  /**
   * Resource record type PTR (domain name pointer)
   */
  const TYPE_PTR = 'PTR';
  /**
   * Resource record type HINFO (host info)
   */
  const TYPE_HINFO = 'HINFO';
  /**
   * Resource record type TXT (text)
   */
  const TYPE_TXT = 'TXT';
  /**
   * Resource record type SRV (service)
   */
  const TYPE_SRV = 'SRV';
  /**
   * Default record time-to-live
   */
  const DEFAULT_TTL = 3600;
  /**
   * SRV RR's protocol TCP
   */
  const PROTO_TCP = '_tcp';
  /**
   * SRV RR's protocol UDP
   */
  const PROTO_UDP = '_udp';

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
      array('idZone,type,ttl', 'required'),
      array('type', 'in', 'range' => array(self::TYPE_A, self::TYPE_AAAA, self::TYPE_CNAME, self::TYPE_HINFO, self::TYPE_MX, self::TYPE_NS, self::TYPE_PTR, self::TYPE_SRV, self::TYPE_TXT)),
      array('host,rdata', 'required', 'on' => 'createType' . self::TYPE_A . ',updateType' . self::TYPE_A . ',createType' . self::TYPE_AAAA . ',updateType' . self::TYPE_AAAA . ',createType' . self::TYPE_CNAME . ',updateType' . self::TYPE_CNAME, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('priority,rdata','required', 'on' => 'createType' . self::TYPE_MX . ',updateType' . self::TYPE_MX, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('host,proto,priority,weight,port,target', 'required', 'on' => 'createType' . self::TYPE_SRV . ',updateType' . self::TYPE_SRV, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('host,rdata', 'required', 'on' => 'createType' . self::TYPE_NS . ',updateType' . self::TYPE_NS . ',createType' . self::TYPE_TXT . ',updateType' . self::TYPE_TXT, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('host', 'match', 'pattern' => '/^@$|^\*$|^[A-Za-z0-9-_]+[A-Za-z0-9-_\.]*$|^\*(\.[A-Za-z0-9-_]+)+$/', 'on' => 'createType' . self::TYPE_A . ',updateType' . self::TYPE_A . ',createType' . self::TYPE_AAAA . ',updateType' . self::TYPE_AAAA . ',createType' . self::TYPE_CNAME . ',updateType' . self::TYPE_CNAME . ',createType' . self::TYPE_TXT . ',updateType' . self::TYPE_TXT, 'message' => Yii::t('error', 'Host name is not valid')),
      array('rdata', 'match', 'pattern' => '/^(((?=(?>.*?::)(?!.*::)))(::)?([0-9A-F]{1,4}::?){0,5}|([0-9A-F]{1,4}:){6})(\2([0-9A-F]{1,4}(::?|$)){0,2}|((25[0-5]|(2[0-4]|1[0-9]|[1-9])?[0-9])(\.|$)){4}|[0-9A-F]{1,4}:[0-9A-F]{1,4})(?<![^:]:)(?<!\.)$/i', 'on' => 'createType' . self::TYPE_AAAA . ',updateType' . self::TYPE_AAAA, 'message' => Yii::t('error', 'Address format is not valid')),
      array('rdata', 'match', 'pattern' => '/^(25[0-4]|2[0-4][0-9]|[0-1]?[0-9][0-9]?){1}(\.(25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)){3}$/', 'on' => 'createType' . self::TYPE_A . ',updateType' . self::TYPE_A, 'message' => Yii::t('error', 'Address format is not valid')),
      array('rdata', 'length', 'max' => 4096),
      array('host,name,target,suffix', 'length', 'max' => 63),
      array('class', 'length', 'max' => 2),
      array('proto', 'length', 'max' => 15),
      array('readonly', 'boolean'),
      array('idZone,ttl,priority,weight,port', 'numerical', 'integerOnly' => true),
    );
  }

  public function relations()
  {
    return array(
      'zone' => array(self::BELONGS_TO, 'Zone', 'idZone'),
    );
  }

  public function beforeValidate()
  {
    if (parent::beforeValidate()) {
      if (empty($this->class)) {
        $this->class = self::DEFAULT_CLASS;
      }

      $domainName = $this->zone->domain->getDomainName();

      if ($this->rdata == '@') {
        $this->rdata = $domainName . '.';
      }

      if ($this->type == self::TYPE_SRV) {
        if (strpos($this->host, '_') !== 0) {
          $this->host = '_' . $this->host;
        }
        $this->name = $this->host . '.' . $this->proto . '.' . ($this->suffix ? $this->suffix . '.' : '') . $domainName . '.';
      }

      if ($this->type == self::TYPE_TXT) {
        if (strpos($this->rdata, '"') !== false) {
          $this->addError('rdata', Yii::t('error', 'TXT-type data can not contain symbol ".'));
          return false;
        }
      }

      if ($this->type == self::TYPE_CNAME) {
        if (stripos($this->host, '@') !== false || $this->host == '*') {
          $this->addError('host', Yii::t('error', 'CNAME for "{host}" is prohibited.',array('{host}' => $this->host)));
          return false;
        }
        if ($this->isNewRecord) {
          $c = self::model()->count('t.idZone=:zone AND t.host=:host', array(
            ':zone' => $this->idZone,
            ':host' => $this->host,
          ));
        }
        else {
          $c = self::model()->count('t.idZone=:zone AND t.host=:host AND t.id<>:id', array(
            ':zone' => $this->idZone,
            ':host' => $this->host,
            ':id'   => $this->id,
          ));
        }
        if ($c) {
          $this->addError('host', Yii::t('error', 'Can not create CNAME for "{host}" - this host name already taken by another resource record.', array('{host}' => $this->host)));
          return false;
        }
      }

      if (in_array($this->type, array(self::TYPE_A, self::TYPE_AAAA, self::TYPE_CNAME))) {
        if (Yii::app()->user->getModel()->plan && Yii::app()->user->getModel()->plan->type == PricingPlan::TYPE_FREE) {
          if (stripos($this->host, '*') !== false) {
            $this->addError('host', Yii::t('error', 'Wildcard resource record is not available in your account type.'));
            return false;
          }
        }
      }

      return true;
    }

    return false;
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      $domainName = strtolower($this->zone->domain->getDomainName(true));
      switch ($this->type) {
        case self::TYPE_CNAME:
        case self::TYPE_MX:
        case self::TYPE_NS:
          if ($this->rdata[strlen($this->rdata)-1] != '.') {
            if (!empty($this->zone->domain->name)) {
              $this->rdata .= '.' . $domainName . '.';
            }
          }
          break;
        case self::TYPE_TXT:
          if (
            $this->host[strlen($this->host)-1] == '.' &&
            stripos($this->host,$domainName . '.') <> strlen($this->host) - strlen($domainName) - 1
          ) {
            $this->host = rtrim($this->host,'.');
          }
          break;
      }

      return true;
    }

    return false;
  }

  public function search($idZone,$type = ResourceRecord::DEFAULT_TYPE)
  {
    $criteria=new CDbCriteria;
    $criteria->compare('t.idZone', $idZone);
    $criteria->compare('t.type', $type);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => false,
      'sort' => array(
        'defaultOrder' => 't.readonly DESC,t.host ASC,t.rdata ASC',
      ),
    ));
  }

  public function attributeLabels()
  {
    return array(
      'host' =>Yii::t('domain', 'Host'),
      'class' =>Yii::t('domain', 'Class'),
      'type' =>Yii::t('domain', 'Type'),
      'rdata' =>Yii::t('domain', 'Points to'),
      'text' =>Yii::t('domain', 'Text entry'),
      'port' =>Yii::t('domain', 'Port'),
      'ttl' =>Yii::t('domain', 'TTL'),
      'service' =>Yii::t('domain', 'Service'),
      'proto' =>Yii::t('domain', 'Protocol'),
      'suffix' =>Yii::t('domain', 'Service name suffix'),
      'priority' =>Yii::t('domain', 'Priority'),
      'weight' =>Yii::t('domain', 'Weight'),
      'target' =>Yii::t('domain', 'Target host'),
    );
  }

  public function beautyTtl($ttl = null)
  {
    if (empty($ttl)) {
      $ttl = $this->ttl;
    }

    return Yii::app()->user->beautyTtl($ttl);
  }
}
