<?php
/**
  Project       : ActiveDNS
  Document      : models/DomainStatMonthly.php
  Document type : PHP script file
  Created at    : 30.08.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain monthly statistics model
*/
/**
 * @property integer $domainID
 * @property integer $year
 * @property integer $month
 * @property integer $requests
 */
class DomainStatMonthly extends CActiveRecord
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
      'year',
      'month',
    );
  }

  public function rules()
  {
    return array(
      array('idDomain,year,month', 'required'),
      array('idDomain,year,month,requests', 'numerical', 'integerOnly'=> true),
    );
  }

  public function relations()
  {
    return array(
      'domain' => array(self::BELONGS_TO, 'Domain', 'idDomain'),
    );
  }
}
