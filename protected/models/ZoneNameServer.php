<?php
/**
  Project       : ActiveDNS
  Document      : models/ZoneNameServer.php
  Document type : PHP script file
  Created at    : 29.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Zone to nameservers bind model
*/
/**
 * @property integer $zoneID zone primary key
 * @property integer $nameServerID nameserver primary key
 */
class ZoneNameServer extends CActiveRecord
{
  /**
   * @param string $className
   * @return ZoneNameServer
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{' . __CLASS__ . '}}';
  }

  public function primaryKey()
  {
    return array(
      'zoneID',
      'nameServerID',
    );
  }

  public function rules()
  {
    return array(
      array('zoneID,nameServerID', 'required'),
      array('zoneID,nameServerID', 'numerical', 'integerOnly' => true),
    );
  }
}
