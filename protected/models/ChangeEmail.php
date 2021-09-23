<?php
/**
  Project       : ActiveDNS
  Document      : models/ChangeEmail.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Change email storage
*/

class ChangeEmail extends CActiveRecord
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
      array('email,newEmail', 'required'),
      array('userID', 'numerical', 'integerOnly'=>true),
      array('email,newEmail', 'email'),
      array('email,newEmail', 'length', 'max'=>255),
      array('newEmail', 'unique', 'attributeName' => 'email', 'className' => 'User', 'skipOnError' => true),
    );
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->activeBefore = time() + self::URL_TTL;
      }

      return true;
    }

    return false;
  }
}
