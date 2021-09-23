<?php
/**
  Project       : ActiveDNS
  Document      : models/Config.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Site configuration model
*/

class Config extends CActiveRecord
{
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
      array('id, value', 'required'),
      array('id', 'length', 'max' => 63),
      array('value', 'length', 'max' => 1022),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('config', 'Variable name'),
      'value' => Yii::t('config', 'Value'),
      'header' => Yii::t('config', 'Site configuration'),
      'buttonSave' => Yii::t('common', 'Save'),
      'buttonCancel' => Yii::t('common', 'Cancel'),
    );
  }

  public static function get($id)
  {
    $model = self::model()->findByPk($id);
    if ($model !== null) return $model->value;
    return false;
  }

  public static function set($id,$value)
  {
    $model = self::model()->findByPk($id);
    if ($model !== null) {
      $model->value = $value;
      $model->save();
    } else {
      $model = new self;
      $model->id = $id;
      $model->value = $value;
      $model->save();
    }

    return $model;
  }
}
