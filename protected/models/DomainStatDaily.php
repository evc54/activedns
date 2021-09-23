<?php
/**
  Project       : ActiveDNS
  Document      : models/DomainStatDaily.php
  Document type : PHP script file
  Created at    : 30.08.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain daily statistics model
*/
/**
 * @property integer $domainID
 * @property string  $date
 * @property integer $requests
 */
class DomainStatDaily extends CActiveRecord
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
    );
  }

  public function rules()
  {
    return array(
      array('idDomain,date', 'required'),
      array('idDomain,requests', 'numerical', 'integerOnly' => true),
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
