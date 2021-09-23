<?php
/**
  Project       : ActiveDNS
  Document      : models/RestoreAccess.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Restore password storage
*/
/**
 * @property string  $email
 * @property integer $timestamp
 * @property integer $activeBefore
 */
class RestoreAccess extends CActiveRecord
{
  const URL_TTL = 86400;

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
      array('email', 'required'),
      array('email', 'length', 'max' => 64),
    );
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->timestamp = time();
        $this->activeBefore = time() + self::URL_TTL;
      }

      return true;
    }

    return false;
  }
}
