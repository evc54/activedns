<?php
/**
  Project       : ActiveDNS
  Document      : models/Zone.php
  Document type : PHP script file
  Created at    : 06.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain's zones model
*/
/**
 * @property integer $id Primary key
 * @property string  $hostmaster Host master e-mail address
 * @property integer $idDomain Domain ID
 * @property integer $idNameServerAlias Active nameservers alias ID
 * @property integer $create Create timestamp
 * @property integer $serial Zone serial number
 * @property integer $refresh Zone SOA refresh interval
 * @property integer $retry Zone SOA retry interval
 * @property integer $expire Zone SOA expire TTL
 * @property integer $minimum Zone SOA minimum TTL
 * @property Domain $domain Domain zone belongs to
 * @property ResourceRecord $record Zone resource records
 */
class Zone extends CActiveRecord
{
  const DEFAULT_REFRESH = 28800;  // slave refresh
  const DEFAULT_RETRY   = 7200;   // slave retry time in case of a problem
  const DEFAULT_EXPIRE  = 3628800;// slave expiration time
  const DEFAULT_MAXIMUM = 3600;   // maximum caching time in case of failed lookups

  /**
   * @param string $className
   * @return Zone
   */
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
      array('idDomain', 'required'),
      array('idDomain,idNameServerAlias,create,serial,refresh,retry,expire,minimum', 'numerical', 'integerOnly' => true),
      array('hostmaster', 'email', 'on' => 'manual'),
      array('refresh', 'numerical', 'min' => 7200, 'max' => 86400, 'tooSmall' => Yii::t('error', 'Refresh time can not be less than 2 hours.'), 'tooBig' => Yii::t('error', 'Refresh time can not be more than 24 hours.'), 'on' => 'manual'),
      array('retry', 'numerical', 'min' => 900, 'max' => 86400, 'tooSmall' => Yii::t('error', 'Retry timeout can not be less than 15 minutes.'), 'tooBig' => Yii::t('error', 'Retry timeout can not be more than 24 hours.'), 'on' => 'manual'),
      array('expire', 'numerical', 'min' => 86400, 'max' => 9676800, 'tooSmall' => Yii::t('error', 'Expiry time can not be less than 1 day.'), 'tooBig' => Yii::t('error', 'Expiry time can not be more than 16 weeks.'), 'on' => 'manual'),
      array('minimum', 'numerical', 'min' => 1, 'max' => 604800, 'tooSmall' => Yii::t('error', 'Minimum time-to-live can not be less than 1 second.'), 'tooBig' => Yii::t('error','Minimum time-to-live can not be more than 16 weeks.'), 'on' => 'manual'),
      array('hostmaster', 'length', 'max' => 64),
    );
  }

  public function relations()
  {
    return array(
      'domain'          => array(self::BELONGS_TO, 'Domain', 'idDomain'),
      'record'          => array(self::HAS_MANY, 'ResourceRecord', 'idZone'),
      'nameserverAlias' => array(self::BELONGS_TO, 'NameServerAlias', 'idNameServerAlias'),
      'nameservers'     => array(self::MANY_MANY, 'NameServer', '{{ZoneNameServer}}(zoneID,nameServerID)'),
    );
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->create = time();
        if (empty($this->hostmaster)) {
          $this->hostmaster = Yii::app()->user->getAttribute('soaHostmaster');
        }
        if (empty($this->refresh)) {
          $this->refresh = self::DEFAULT_REFRESH;
        }
        if (empty($this->retry)) {
          $this->retry = self::DEFAULT_RETRY;
        }
        if (empty($this->expire)) {
          $this->expire = self::DEFAULT_EXPIRE;
        }
        if (empty($this->minimum)) {
          $this->minimum = self::DEFAULT_MAXIMUM;
        }
      }

      $this->hostmaster = str_replace('@', '.', rtrim($this->hostmaster, ' .')) . '.';

      return true;
    }

    return false;
  }

  public function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if ($this->record) {
        foreach ($this->record as $record) {
          $record->delete();
        }
      }

      ZoneNameServer::model()->deleteAllByAttributes(array('zoneID' => $this->id));

      return true;
    }

    return false;
  }

  public function getUserNS()
  {
    return ResourceRecord::model()->findAll(array(
      'condition' => "t.idZone=:idZone AND t.type=:rrType AND t.readonly=:readonly",
      'params' => array(
        ':idZone' => $this->id,
        ':rrType' => ResourceRecord::TYPE_NS,
        ':readonly' => false,
      ),
      'order' => 't.id ASC',
    ));
  }

  public function getPrimaryNS()
  {
    $primary = '';

    $nameservers = $this->domain->getDomainNameservers();
    if (is_array($nameservers)) {
      $primary = current($nameservers);
    }

    return $primary;
  }

  public function getTtlSuffix()
  {
    // multiplier => time period
    return array(
      604800 => Yii::t('common', 'week(s)'),
      86400 => Yii::t('common', 'day(s)'),
      3600 => Yii::t('common', 'hour(s)'),
      60 => Yii::t('common', 'minute(s)'),
    );
  }

  public function getSuffixValue($ttl)
  {
    $suffixes = $this->getTtlSuffix();
    foreach ($suffixes as $limit => $value) {
      if ((($ttl / $limit) >= 1) && (($ttl % $limit) >= 0)) return $limit;
    }
    return $limit;
  }

  public function getZoneSOA($params)
  {
    $template = '$ORIGIN .' . PHP_EOL;
    $template .= '$TTL {minimum}' . PHP_EOL;
    $template .= '{domain} IN SOA {primary}. {hostmaster} (' . PHP_EOL;
    $template .= '                               {serial} ; serial' . PHP_EOL;
    $template .= '                               {refresh} ; refresh' . PHP_EOL;
    $template .= '                               {retry} ; retry' . PHP_EOL;
    $template .= '                               {expire} ; expire' . PHP_EOL;
    $template .= '                               {minimum} ; minimum' . PHP_EOL;
    $template .= ')' . PHP_EOL;

    foreach ($params as $key => $value) {
      $template = str_replace($key, $value, $template);
    }

    return $template;
  }

  public function generateZoneRRFile()
  {
    $domain = $this->domain->getDomainName();
    $soa = $this->getZoneSOA(array(
      '{domain}'     => $domain,
      '{primary}'    => $this->getPrimaryNS(),
      '{hostmaster}' => $this->hostmaster,
      '{serial}'     => $this->serial,
      '{refresh}'    => $this->refresh,
      '{retry}'      => $this->retry,
      '{expire}'     => $this->expire,
      '{minimum}'    => $this->minimum,
    ));

    $rootRR = array();
    $domainRR = array();
    $wildcardRR = array();
    foreach ($this->record as $record) {
      if (empty($record->host) || ($record->host == '@')) {
        $rootRR[] = $record->attributes;
      }
      elseif (stripos($record->host, '*') !== false) {
        $wildcardRR[] = $record->attributes;
      }
      else {
        $domainRR[] = $record->attributes;
      }
    }

    $rootResourceRecords = '';
    foreach ($rootRR as $record) {
      $rootResourceRecords .= $this->getResourceRecordText($record);
    }

    $domainResourceRecords = '';
    foreach ($domainRR as $record) {
      $domainResourceRecords .= $this->getResourceRecordText($record);
    }

    $globalWildcard = '';

    foreach ($wildcardRR as $record) {
      if ($record['host'] == '*') {
        $globalWildcard .= $this->getResourceRecordText($record);
      }
      else {
        $domainResourceRecords .= $this->getResourceRecordText($record);
      }
    }

    if (!empty($globalWildcard)) {
      $domainResourceRecords .= $globalWildcard;
    }

    return $soa . $rootResourceRecords . '$ORIGIN ' . $domain . '.' . PHP_EOL . $domainResourceRecords;
  }

  private function getResourceRecordText($record)
  {
    $resourceRecord = '';
    $record['host'] = str_replace('@', '', $record['host']);
    switch ($record['type']) {
      case ResourceRecord::TYPE_A:
      case ResourceRecord::TYPE_AAAA:
      case ResourceRecord::TYPE_NS:
      case ResourceRecord::TYPE_CNAME:
        $resourceRecord .= sprintf("%-18s %-7s %-7s %-7s %s\n", $record['host'], $record['ttl'], $record['class'], $record['type'], $record['rdata']);
        break;
      case ResourceRecord::TYPE_MX:
        $resourceRecord .= sprintf("%-18s %-7s %-7s %s %-4s %s\n", $record['host'], $record['ttl'], $record['class'], $record['type'], $record['priority'], $record['rdata']);
        break;
      case ResourceRecord::TYPE_TXT:
        $rdata = $record['rdata'];
        if (mb_strlen($rdata, Yii::app()->charset) > 255) {
          $rdata = explode("\n",wordwrap($rdata, 255, "\n", true));
          $rdata = '("' . implode('" "', $rdata) . '")';
        }
        else {
          $rdata = '"' . $rdata . '"';
        }
        $resourceRecord .= sprintf("%-18s %-7s %-7s %-7s %s\n", $record['host'], $record['ttl'], $record['class'], $record['type'], $rdata);
        break;
      case ResourceRecord::TYPE_SRV:
        $resourceRecord .= sprintf("%-18s %-7s %-7s %-7s %s %s %s %s\n", $record['host'] . '.' . $record['proto'] . ($record['suffix'] ? '.' . $record['suffix'] : ''), $record['ttl'], $record['class'], $record['type'], $record['priority'], $record['weight'], $record['port'], $record['target']);
        break;
    }

    return $resourceRecord;
  }

  public function getAddressList($hostname)
  {
    $list = array();

    $records = ResourceRecord::model()->findAll(array(
      'condition' => "t.idZone=:idZone AND t.host=:hostname AND (t.type=:rrTypeA OR t.type=:rrTypeAAAA)",
      'params' => array(
        ':idZone' => $this->id,
        ':hostname' => $hostname,
        ':rrTypeA' => ResourceRecord::TYPE_A,
        ':rrTypeAAAA' => ResourceRecord::TYPE_AAAA,
      ),
    ));

    if ($records) {
      foreach ($records as $record) {
        $list[] = $record->rdata;
      }
    }

    return $list;
  }
}
