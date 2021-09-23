<?php
/**
  Project       : ActiveDNS
  Document      : models/NameServer.php
  Document type : PHP script file
  Created at    : 14.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameservers model
*/
/**
 * @property integer $id primary key
 * @property integer $status nameserver status
 * @property string  $name namserver host name
 * @property string  $address IP addresses list
 * @property integer $type nameserver type - master or slave
 * @property integer $idNameServerPair for slave nameserver here stored master's ID
 * @property string  $token security token for statistics upload
 * @property integer $lastStatUpload timestamp indicating last statistics upload
 */
class NameServer extends CActiveRecord
{
  const ORDER_MOST_LOADED = 'DESC';
  const ORDER_MOST_FREE = 'ASC';

  const TYPE_MASTER = 1;
  const TYPE_SLAVE = 2;

  const STATUS_ENABLED = 1;
  const STATUS_DISABLED = 0;

  const PAGESIZE = 10;
  
  private $_load;

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
      array('name,type,token', 'required'),
      array('name', 'length', 'max'=>64),
      array('token', 'length', 'max'=>32),
      array('address', 'length', 'max'=>1000),
      array('publicAddress', 'length', 'max'=>1000),
      array('idNameServerPair,lastStatUpload', 'numerical', 'integerOnly' => true),
      array('type', 'in', 'range' => array(self::TYPE_MASTER, self::TYPE_SLAVE)),
    );
  }

  public function relations()
  {
    return array(
      'zones'  => array(self::MANY_MANY, 'Zone', '{{Zone}}(nameServerID,zoneID)'),
      'master' => array(self::BELONGS_TO, 'NameServer', 'idNameServerPair'),
      'pairs'  => array(self::HAS_MANY, 'NameServer', 'idNameServerPair'),
    );
  }

  public function beforeValidate()
  {
    if (empty($this->address)) {
      $this->address = gethostbyname($this->name);
    }

    return true;
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      foreach (array('address', 'publicAddress') as $attribute) {
        $addresses = explode("\n", str_replace(array(" ", "\r\n", "\n", "\t", ","), "\n", $this->getAttribute($attribute)));
        if (is_array($addresses) && count($addresses)) {
          $list = array();
          foreach ($addresses as $address) {
            $address = trim($address, ' .,');
            if (!empty($address)) {
              $list[] = $address;
            }
          }
          $this->setAttribute($attribute, implode("\n", $list));
        }
      }

      return true;
    }

    return false;
  }
  
  public function beforeDelete()
  {
    if ($this->load > 0) {
      $this->addError('name', Yii::t('error', 'Cannot delete nameserver - it is loaded with {n} zone|Cannot delete nameserver - it is loaded with {n} zones', array($this->load)));
      return false;
    }

    return true;
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'status' => Yii::t('nameserver', 'Status'),
      'name' => Yii::t('nameserver', 'Hostname'),
      'address' => Yii::t('nameserver', 'IP Addresses'),
      'publicAddress' => Yii::t('nameserver', 'Public IP Addresses'),
      'type' => Yii::t('nameserver', 'Type'),
      'load' => Yii::t('nameserver', 'Load'),
      'idNameServerPair' => Yii::t('nameserver', 'Pairings'),
      'token' => Yii::t('nameserver', 'Security token'),
      'lastStatUpload' => Yii::t('nameserver', 'Last statistic upload'),
    );
  }

  public function attributeLabelsType()
  {
    return array(
      self::TYPE_MASTER => Yii::t('nameserver', 'Master'),
      self::TYPE_SLAVE  => Yii::t('nameserver', 'Slave'),
    );
  }

  public function getAttributeLabelType($type = null)
  {
    if ($type === null) {
      $type = $this->type;
    }

    $labels = $this->attributeLabelsType();

    return isset($labels[$type]) ? $labels[$type] : '';
  }

  public function attributeLabelsStatus()
    {
    return array(
      self::STATUS_DISABLED => Yii::t('nameserver', 'Disabled'),
      self::STATUS_ENABLED  => Yii::t('nameserver', 'Enabled'),
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
  
  public function getLoad()
  {
    if ($this->_load === null) {
      $criteria = new CDbCriteria;
      $criteria->with = array(
        'currentZone',
        'currentZone.nameservers',
      );
      $criteria->compare('nameservers.id', $this->id);
      $this->_load = Domain::model()->count($criteria);
    }
    
    return $this->_load;
  }

  public function getStatusClass($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }

    $classes = array(
      self::STATUS_DISABLED => 'disabled',
      self::STATUS_ENABLED  => 'enabled',
    );

    return (isset($classes[$status])) ? $classes[$status] : '';
  }
  
  public function getMasterNameservers()
  {
    $nameservers = array();

    $criteria = new CDbCriteria;
    $criteria->with = array('pairs');
    $criteria->compare('t.status', self::STATUS_ENABLED);
    $criteria->compare('t.type', self::TYPE_MASTER);
    
    $ns = self::model()->findAll($criteria);
    
    if (!empty($ns)) {
      foreach ($ns as $server) {
        $pairs = array();
        if (!empty($server->pairs)) {
          foreach ($server->pairs as $pair) {
            $pairs[$pair->id] = $pair->name;
          }
        }
        $nameservers[$server->id] = array(
          'name'  => $server->name,
          'pairs' => $pairs,
        );
      }
    }
    
    return $nameservers;
  }

  public function disable()
  {
    $this->status = self::STATUS_DISABLED;

    return $this->save();
  }

  public function enable()
  {
    $this->status = self::STATUS_ENABLED;

    return $this->save();
  }

  public function search()
  {
    $sort = new CSort;
    $sort->attributes = array(
      'id',
      'status',
      'type',
      'name',
      'address',
      'idNameServerPair',
      'load' => array(
        'asc' => '`load` ASC',
        'desc' => '`load` DESC',
      ),
    );
    $sort->defaultOrder = 't.id DESC';

    $criteria = new CDbCriteria;
    $criteria->join = 'LEFT OUTER JOIN (SELECT d.nameServerID AS nameServerID,COUNT(*) AS `load` FROM {{ZoneNameServer}} d GROUP BY nameServerID) d ON (d.nameServerID = t.id)';
    $criteria->with = array(
      'pairs',
    );
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.type', $this->type);
    $criteria->compare('t.name', $this->name, true);
    $criteria->compare('t.address', $this->address, true);
    $criteria->compare('pairs.id', $this->idNameServerPair, true);
    $criteria->compare('pairs.name', $this->idNameServerPair, true, 'OR');

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => $sort,
    ));
  }

  public function getDir()
  {
    $dir = dirname(Yii::app()->basePath) . DIRECTORY_SEPARATOR . 'nsd' . DIRECTORY_SEPARATOR . $this->name;

    return $this->regenDir($dir);
  }

  public function getZoneDir()
  {
    return $this->regenDir($this->getDir() . DIRECTORY_SEPARATOR . 'zone.conf.d');
  }

  public function getZoneFileDir()
  {
    return $this->regenDir($this->getDir() . DIRECTORY_SEPARATOR . 'zonefile.conf.d');
  }

  public function regenDir($dir)
  {
    if (!file_exists($dir)) {
      @mkdir($dir);
    }

    return $dir;
  }

  public function templateZoneConfigFile($domain)
  {
    $template = '';
    $template .= 'zone:' . PHP_EOL;
    $template .= '  name: "{domain}"' . PHP_EOL;
    $template .= '  zonefile: "{domain}"' . PHP_EOL;
    
    $domainNS = array_keys($domain->getDomainNameservers());

    if ($this->type == self::TYPE_MASTER) {
      $template .= '  notify-retry: 10' . PHP_EOL;
    }

    switch ($this->type) {
      case self::TYPE_MASTER:
        if ($this->pairs) {
          foreach ($this->pairs as $pair) {
            if (in_array($pair->id, $domainNS)) {
              foreach (explode("\n", $pair->address) as $address) {
                if (!empty($address)) {
                  $template .= '  notify: ' . $address . ' "PAIR"' . PHP_EOL;
                  $template .= '  provide-xfr: ' . $address . ' "PAIR"' . PHP_EOL;
                }
              }
            }
          }
        }
        break;
      case self::TYPE_SLAVE:
        if ($this->master) {
          if (in_array($this->master->id, $domainNS)) {
            foreach (explode("\n", $this->master->address) as $address) {
              if (!empty($address)) {
                $template .= '  allow-notify: ' . $address . ' "PAIR"' . PHP_EOL;
                $template .= '  request-xfr: ' . $address . ' "PAIR"' . PHP_EOL;
              }
            }
          }
        }
        break;
    }

    return $template;
  }

  /**
   * Generate zone configuration file
   * 
   * @param Domain $domain Domain model
   * @return string text of zone configuration
   */
  public function generateZoneConfigFile($domain)
  {
    $config = $this->templateZoneConfigFile($domain);
    // additional xfr if user specified his own nameserver as slaves
    $nameservers = $domain->currentZone->getUserNS();
    foreach ($nameservers as $nameserver) {
      if (stripos($nameserver->rdata,$domain->getDomainName()) !== false) {
        $hostname = explode('.',$nameserver->rdata);
        $hostname = array_shift($hostname);
        $address = $domain->currentZone->getAddressList($hostname);
      }
      if (empty($address)) {
        $address = gethostbynamel(rtrim($nameserver->rdata,'.'));
      }
      if (!empty($address)) {
        $address = array_unique($address);
        foreach ($address as $ip) {
          $config .= '  notify: ' . $ip . ' NOKEY' . PHP_EOL;
          $config .= '  provide-xfr: ' . $ip . ' NOKEY' . PHP_EOL;
        }
      }
      if ($domain->transfer) {
        foreach ($domain->transfer as $transfer) {
          if ($transfer->allowNotify) {
            $config .= '  notify: ' . $transfer->address . ' NOKEY' . PHP_EOL;
          }
          if ($transfer->allowTransfer) {
            $config .= '  provide-xfr: ' . $transfer->address . ' NOKEY' . PHP_EOL;
          }
        }
      }
    }
    return str_replace('{domain}', $domain->getDomainName(), $config);
  }

  public function regenerateZoneListFile()
  {
    $content = "# DO NOT MODIFY THIS FILE! IT'S AUTOGENERATED!" . PHP_EOL;
    $content .= "# Generated at: " . gmdate('r') . PHP_EOL;
    $dir = $this->getDir();
    $zoneDir = $this->getZoneDir();
    $files = glob($zoneDir . DIRECTORY_SEPARATOR . '*');
    foreach ($files as $file) {
      if (!is_file($file)) continue;
      $content .= str_replace($zoneDir,'include: "/etc/nsd/conf.d/zone.conf.d',$file) . '"' . PHP_EOL;
    }

    return file_put_contents($dir . DIRECTORY_SEPARATOR . 'zones.conf', $content);
  }

  public function replicate()
  {
    $dir = $this->getDir();
    $return = '';
    $addresses = explode("\n", $this->address);

    foreach ($addresses as $address) {
      if (!empty($address)) {
        $return .= $this->execRemoteCommand("rsync -e 'ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i %key%' -az --delete-after {$dir}/* nsd@{$address}:/etc/nsd/conf.d");
      }
    }
    
    return $return;
  }

  public function restartDaemon()
  {
    $return = '';
    $addresses = explode("\n", $this->address);

    foreach ($addresses as $address) {
      if (!empty($address)) {
        $return .= $this->execRemoteCommand("ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i %key% nsd@{$address} 'sudo service nsd restart'");
      }
    }
    
    return $return;
  }

  private function execRemoteCommand($command)
  {
    $output = false;
    
    $key = $this->getSshKey();
    if ($key) {
      $command = str_replace('%key%', $key, $command);
      exec($command, $output);
    }

    return is_array($output) ? implode("\n", $output) : $output;
  }

  public function cleanZoneFiles()
  {
    $dir = $this->getDir();
    exec("rm -rf {$dir}");
  }

  public function getSshKey()
  {
    $key = Yii::app()->basePath . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'keys' . DIRECTORY_SEPARATOR . 'remote';
    if (!file_exists($key)) {
      Yii::log('Cannot find remote host private key in ' . $key . ' file', CLogger::LEVEL_ERROR);
      return false;
    }
    return $key;
  }
}
