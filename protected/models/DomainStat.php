<?php
/**
  Project       : ActiveDNS
  Document      : models/DomainStat.php
  Document type : PHP script file
  Created at    : 20.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain statistics model
*/
/**
 * @property integer $idDomain
 * @property string  $date
 * @property integer $hour
 * @property integer $requests
 */
class DomainStat extends CActiveRecord
{
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
      'idDomain',
      'date',
      'hour',
    );
  }

  public function rules()
  {
    return array(
      array('idDomain,date,hour', 'required'),
      array('idDomain,hour,requests', 'numerical', 'integerOnly' => true),
      array('date', 'date', 'format' => 'yyyy-MM-dd'),
    );
  }

  public function relations()
  {
    return array(
      'domain' => array(self::BELONGS_TO, 'Domain', 'idDomain'),
    );
  }
}
