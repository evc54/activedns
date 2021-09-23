<?php
/**
  Project       : ActiveDNS
  Document      : models/Template.php
  Document type : PHP script file
  Created at    : 14.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Templates model
*/
/**
 * @property integer $id
 * @property integer $idUser
 * @property integer $type
 * @property string  $name
 * @property integer $created
 * @property integer $updated
 */
class Template extends CActiveRecord
{
  const TYPE_COMMON = 0;
  const TYPE_PRIVATE = 1;

  const PRIORITY_ZONE = 1;
  const PRIORITY_TEMPLATE = 2;

  const PAGESIZE = 30;

  /**
   * @param string $className
   * @return Template
   */
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
      array('type,name', 'required'),
      array('type', 'in', 'range' => array(self::TYPE_COMMON, self::TYPE_PRIVATE)),
      array('idUser,created', 'numerical', 'integerOnly' => true),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'idUser' => Yii::t('template', 'Author'),
      'type' => Yii::t('template', 'Type'),
      'name' => Yii::t('template', 'Name'),
      'created' => Yii::t('template', 'Created at'),
      'updated' => Yii::t('template', 'Updated at'),
      'recordsQty' => Yii::t('template', 'Records'),
    );
  }

  public function relations()
  {
    return array(
      'owner'      => array(self::BELONGS_TO, 'User', 'idUser'),
      'records'    => array(self::HAS_MANY, 'TemplateRecord', 'templateID'),
      'recordsQty' => array(self::STAT, 'TemplateRecord', 'templateID'),
    );
  }

  public function own()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('idUser', Yii::app()->user->id);
    $this->getDbCriteria()->mergeWith($criteria);

    return $this;
  }

  public function common()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('type', self::TYPE_COMMON);
    $this->getDbCriteria()->mergeWith($criteria);

    return $this;
  }

  public function select()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('idUser', Yii::app()->user->id);
    $criteria->compare('type', self::TYPE_PRIVATE, false);
    $criteria->compare('type', self::TYPE_COMMON, false, 'OR');
    $this->getDbCriteria()->mergeWith($criteria);

    return $this;
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->created = time();
        if ($this->type == self::TYPE_PRIVATE && !$this->idUser) {
          $this->idUser = Yii::app()->user->id;
        }
      }
      $this->updated = time();

      return true;
    }

    return false;
  }

  public function beforeDelete()
  {
    if (parent::beforeDelete()) {

      TemplateRecord::model()->deleteAllByAttributes(array('templateID' => $this->id));

      return true;
    }

    return false;
  }

  public function attributeLabelsType()
  {
    return array(
      self::TYPE_COMMON  => Yii::t('template', 'Common'),
      self::TYPE_PRIVATE => Yii::t('template', 'Private'),
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

  public function search()
  {
    $criteria = new CDbCriteria;
    if ($this->type != Template::TYPE_COMMON) {
      if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN) {
        $criteria->with = array(
          'owner',
          'recordsQty',
        );
        if (empty($this->idUser)) {
          $criteria->compare('t.idUser','<>0');
        }
        else {
          $criteria->compare('owner.id', $this->idUser, true);
          $criteria->compare('owner.email', $this->idUser, true, 'OR');
        }
      }
      else {
        $criteria->compare('t.idUser', Yii::app()->user->id);
      }
    }
    else {
        $criteria->compare('t.idUser', '0');
    }
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.type', $this->type);
    $criteria->compare('t.name', $this->name, false);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.created') . ' DESC',
      ),
    ));
  }

  public function hasRecords($type)
  {
    foreach ($this->records as $record) {
      if ($record->type == $type) {
        return true;
      }
    }

    return false;
  }

  public function getRecords($type)
  {
    $result = array();

    foreach ($this->records as $record) {
      if ($record->type == $type) {
        $result[] = $record;
      }
    }

    return $result;
  }
}
