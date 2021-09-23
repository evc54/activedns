<?php
/**
  Project       : ActiveDNS
  Document      : models/DomainZoneTransfer.php
  Document type : PHP script file
  Created at    : 10.06.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Additional zone transfer/notify configuration model
*/
/**
 * @property integer $id primary key
 * @property integer $domainID domain ID
 * @property string  $address IPv4 or IPv6 address
 * @property boolean $allowNotify allow notify address
 * @property boolean $allowTransfer allow zone transfer to address
 */
class DomainTransfer extends CActiveRecord
{
  /**
   * Return model class object
   * @param string $className
   * @return ZoneTransfer
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  /**
   * Table name for this model
   * @return string
   */
  public function tableName()
  {
    return '{{' . __CLASS__ . '}}';
  }

  /**
   * Object relations
   * @return array
   */
  public function relations()
  {
    return array(
      'domain'=>array(self::BELONGS_TO, 'Domain', 'domainID'),
    );
  }

  /**
   * Validation rules
   * @return array
   */
  public function rules()
  {
    return array(
      array('domainID,address', 'required'),
      array('domainID', 'numerical', 'integerOnly' => true),
      array('allowNotify,allowTransfer','boolean'),
      array('address', 'match', 'pattern' => '/^((((?=(?>.*?::)(?!.*::)))(::)?([0-9A-F]{1,4}::?){0,5}|([0-9A-F]{1,4}:){6})(\2([0-9A-F]{1,4}(::?|$)){0,2}|((25[0-5]|(2[0-4]|1[0-9]|[1-9])?[0-9])(\.|$)){4}|[0-9A-F]{1,4}:[0-9A-F]{1,4})(?<![^:]:)(?<!\.))|((25[0-4]|2[0-4][0-9]|[0-1]?[0-9][0-9]?){1}(\.(25[0-5]|2[0-4][0-9]|[0-1]?[0-9][0-9]?)){3})$/i','message' => Yii::t('error', 'Address format is not valid')),
    );
  }

  /**
   * Hold labels for model attributes
   * @return array
   */
  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'address' => Yii::t('domain', 'Target host IPv4 or IPv6 address'),
      'allowNotify' => Yii::t('domain', 'Allow notify'),
      'allowTransfer' => Yii::t('domain', 'Allow transfer'),
    );
  }
}
