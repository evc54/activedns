<?php
/**
  Project       : ActiveDNS
  Document      : models/Alert.php
  Document type : PHP script file
  Created at    : 14.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Alerts model
*/
/**
 * @property integer $idUser
 * @property integer $idDomain
 * @property integer $create
 * @property string  $alert
 * @property boolean $notified User has been notified about this alert
 */
class Alert extends CActiveRecord
{
  const TYPE_WRONG_NAMESERVERS = 1;
  const TYPE_DOMAIN_EXPIRED = 2;
  const TYPE_DOMAIN_NOT_REGISTERED = 3;
  const TYPE_WRONG_NAMESERVERS_ALIASES = 4;

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
      array('idUser,alert', 'required'),
      array('idUser,type', 'numerical', 'integerOnly' => true),
      array('alert', 'length', 'max' => 1022),
    );
  }

  public function relations()
  {
    return array(
      'domain' => array(self::BELONGS_TO, 'Domain', 'idDomain'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'idUser' => Yii::t('alerts', 'Owner'),
      'create' => Yii::t('alerts', 'Appeared'),
      'alert'  => Yii::t('alerts', 'Alert'),
    );
  }

  public function getSolution()
  {
    switch ($this->type) {
      case self::TYPE_WRONG_NAMESERVERS:
        return Yii::t('alerts', "Set assigned nameservers for domain in registrar's domain manager panel");

      case self::TYPE_WRONG_NAMESERVERS_ALIASES:
        return Yii::t('alerts', "Set the right A-type records for nameserver's aliases");

      case self::TYPE_DOMAIN_NOT_REGISTERED:
        return Yii::t('alerts', 'Register domain with any registrar, e.g. {link}',array('{link}' => CHtml::link('godaddy.com', 'http://x.co/oNq5')));

      case self::TYPE_DOMAIN_EXPIRED:
        return Yii::t('alerts', "Renew domain through registrar's website");
    }
  }
}
