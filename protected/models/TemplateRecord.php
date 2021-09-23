<?php
/**
  Project       : ActiveDNS
  Document      : models/TemplateRecord.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Template records
*/
/**
 * @property integer $templateID
 */
class TemplateRecord extends CActiveRecord
{
  /**
   * @param string $className
   * @return TemplateRecord
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
      array('templateID,type,ttl', 'required'),
      array('type', 'in', 'range' => array(ResourceRecord::TYPE_A, ResourceRecord::TYPE_AAAA, ResourceRecord::TYPE_CNAME, ResourceRecord::TYPE_HINFO, ResourceRecord::TYPE_MX, ResourceRecord::TYPE_NS,ResourceRecord::TYPE_PTR, ResourceRecord::TYPE_SRV, ResourceRecord::TYPE_TXT)),
      array('host,rdata', 'required', 'on' => 'createType' . ResourceRecord::TYPE_A . ',createType' . ResourceRecord::TYPE_AAAA . ',createType' . ResourceRecord::TYPE_CNAME, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('priority,rdata', 'required', 'on' => 'createType' . ResourceRecord::TYPE_MX, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('host,proto,priority,weight,port,target', 'required', 'on' => 'createType' . ResourceRecord::TYPE_SRV, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('host,rdata','required', 'on' => 'createType' . ResourceRecord::TYPE_NS . ',createType' . ResourceRecord::TYPE_TXT, 'message' => Yii::t('error', 'This field cannot be blank')),
      array('rdata', 'length', 'max' => 163),
      array('host,name,target,suffix', 'length', 'max' => 63),
      array('class', 'length', 'max' => 2),
      array('proto', 'length', 'max' => 15),
      array('templateID,ttl,priority,weight,port', 'numerical', 'integerOnly' => true),
    );
  }

  public function relations()
  {
    return array(
      'template' => array(self::BELONGS_TO, 'Template', 'templateID'),
    );
  }

  public function beforeValidate()
  {
    parent::beforeValidate();

    if ($this->type == ResourceRecord::TYPE_CNAME && stripos($this->host, '@') !== false) {
      $this->addError('host', Yii::t('error', 'Can not create CNAME for "{host}" - this host name already taken by another resource record.', array('{host}' => $this->host)));
      return false;
    }

    if (empty($this->class)) {
      $this->class = ResourceRecord::DEFAULT_CLASS;
    }

    if ($this->type == ResourceRecord::TYPE_SRV) {
      if (strpos($this->host, '_') !== 0) {
        $this->host = '_' . $this->host;
      }
      $this->name = $this->host . '.' . $this->proto . ($this->suffix ? '.' . $this->suffix : '');
    }

    return true;
  }

  public function search($templateID,$type = ResourceRecord::DEFAULT_TYPE)
  {
    $criteria=new CDbCriteria;
    $criteria->compare('templateID', $templateID);
    $criteria->compare('type', $type);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  public function attributeLabels()
  {
    return array(
      'host' => Yii::t('domain', 'Host'),
      'class' => Yii::t('domain', 'Class'),
      'type' => Yii::t('domain', 'Type'),
      'rdata' => Yii::t('domain', 'Points to'),
      'port' => Yii::t('domain', 'Port'),
      'priority' => Yii::t('domain', 'Priority'),
      'weight' => Yii::t('domain', 'Weight'),
      'proto' => Yii::t('domain', 'Proto'),
      'suffix' => Yii::t('domain', 'Service name suffix'),
      'target' => Yii::t('domain', 'Target host'),
      'ttl' => Yii::t('domain', 'TTL'),
    );
  }

  public function beautyTtl($ttl = null)
  {
    if (empty($ttl)) {
      $ttl = $this->ttl;
    }

    return Yii::app()->user->beautyTtl($ttl);
  }
}
