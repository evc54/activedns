<?php
/**
  Project       : ActiveDNS
  Document      : models/PricingPlan.php
  Document type : PHP script file
  Created at    : 15.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plan model
*/
/**
 * @property integer $id primary key
 * @property integer $status plan status - enabled or not
 * @property integer $type plan type - free, private, corporate
 * @property integer $domainsQty max. domains qty allowed, -1 for no limit
 * @property integer $usersQty max. users allowed, -1 for no limit
 * @property integer $nameserversQty nameservers qty, 2 or 4
 * @property integer $minTtl minimum ttl in seconds
 * @property boolean $accessApi API access allowed or not
 * @property float   $pricePerYear price per year in local currency
 * @property float   $pricePerMonth price per month in local currency
 * @property integer $billing billing cycle - monthly or annually
 * @property integer $defaultNameserverMaster assigned master nameserver
 * @property integer $defaultNameserverSlave1 assigned first slave nameserver
 * @property integer $defaultNameserverSlave2 assigned second slave nameserver
 * @property integer $defaultNameserverSlave3 assigned third slave nameserver
 */
class PricingPlan extends CActiveRecord
{
  const TYPE_FREE = 0;
  const TYPE_PRIVATE = 1;
  const TYPE_CORPORATE = 2;

  const STATUS_ENABLED = 1;
  const STATUS_DISABLED = 0;

  const BILLING_ANNUALLY = 1;
  const BILLING_MONTHLY = 2;

  const PAGESIZE = 10;

  /**
   * Return model class object
   * @param string $className
   * @return PricingPlan
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
   * Validation rules
   * @return array
   */
  public function rules()
  {
    return array(
      array('status,type,title,domainsQty,usersQty,nameserversQty,minTtl,accessApi,defaultNameserverMaster,defaultNameserverSlave1', 'required'),
      array('title', 'length', 'max' => 255),
      array('domainsQty,usersQty,nameserversQty,minTtl', 'numerical', 'integerOnly' => true),
      array('pricePerYear,pricePerMonth,defaultNameserverMaster,defaultNameserverSlave1,defaultNameserverSlave2,defaultNameserverSlave3', 'numerical'),
      array('accessApi', 'boolean'),
      array('type', 'in', 'range' => array(self::TYPE_FREE, self::TYPE_PRIVATE, self::TYPE_CORPORATE)),
      array('status', 'in', 'range' => array(self::STATUS_DISABLED, self::STATUS_ENABLED)),
      array('billing', 'in', 'range' => range(0,3)),
      array('id,status,type,title,domainsQty,usersQty,nameserversQty,minTtl,accessApi,pricePerYear,pricePerMonth,billing', 'safe', 'on' => 'search'),
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
      'status' => Yii::t('pricingPlan', 'Status'),
      'type' => Yii::t('pricingPlan', 'Type'),
      'title' => Yii::t('pricingPlan', 'Title'),
      'domainsQty' => Yii::t('pricingPlan', 'Domains quantity'),
      'usersQty' => Yii::t('pricingPlan', 'Users quantity'),
      'nameserversQty' => Yii::t('pricingPlan', 'Nameservers quantity'),
      'defaultNameserverMaster' => Yii::t('pricingPlan', 'Default master nameserver'),
      'defaultNameserverSlave1' => Yii::t('pricingPlan', 'Default slave nameserver #1'),
      'defaultNameserverSlave2' => Yii::t('pricingPlan', 'Default slave nameserver #2'),
      'defaultNameserverSlave3' => Yii::t('pricingPlan', 'Default slave nameserver #3'),
      'minTtl' => Yii::t('pricingPlan', 'Min TTL'),
      'accessApi' => Yii::t('pricingPlan', 'API access'),
      'pricePerYear' => Yii::t('pricingPlan', 'Price per year'),
      'pricePerMonth' => Yii::t('pricingPlan', 'Price per month'),
      'billing' => Yii::t('pricingPlan', 'Billing cycle'),
    );
  }

  /**
   * Hold labels for type attribute
   * @return array
   */
  public function attributeLabelsType()
  {
    return array(
      self::TYPE_FREE      => Yii::t('pricingPlan', 'Free'),
      self::TYPE_PRIVATE   => Yii::t('pricingPlan', 'Premium'),
      self::TYPE_CORPORATE => Yii::t('pricingPlan', 'Corporate'),
    );
  }

  /**
   * Return label for provided plan type
   * @param integer $type plan type
   * @return string
   */
  public function getAttributeLabelType($type = null)
  {
    if ($type === null) {
      $type = $this->type;
    }

    $labels = $this->attributeLabelsType();

    return isset($labels[$type]) ? $labels[$type] : '';
  }

  /**
   * Hold labels for billing attribute
   * @return array
   */
  public function attributeLabelsBilling()
  {
    return array(
      0                       => Yii::t('pricingPlan', 'Off'),
      self::BILLING_ANNUALLY  => Yii::t('pricingPlan', 'Annually'),
      self::BILLING_MONTHLY   => Yii::t('pricingPlan', 'Monthly'),
      self::BILLING_ANNUALLY
      + self::BILLING_MONTHLY => Yii::t('pricingPlan', 'Annually, monthly'),
    );
  }

  /**
   * Generate billing options array for futher use
   * @return array
   */
  public function getBillingOptions()
  {
    $labels = $this->attributeLabelsBilling();
    $return = array();

    if ($this->pricePerYear) {
      $return[self::BILLING_ANNUALLY] = $labels[self::BILLING_ANNUALLY];
    }
    if ($this->pricePerMonth > 0) {
      $return[self::BILLING_MONTHLY] = $labels[self::BILLING_MONTHLY];
    }

    return $return;
  }

  /**
   * Return label for provided billing cycle
   * @param integer $billing billing cycle
   * @return string
   */
  public function getAttributeLabelBilling($billing = null)
  {
    if ($billing === null) {
      $billing = $this->billing;
    }

    $labels = $this->attributeLabelsBilling();

    return isset($labels[$billing]) ? $labels[$billing] : '';
  }

  /**
   * Hold labels for status attribute
   * @return array
   */
  public function attributeLabelsStatus()
  {
    return array(
      self::STATUS_DISABLED => Yii::t('pricingPlan', 'Disabled'),
      self::STATUS_ENABLED  => Yii::t('pricingPlan', 'Enabled'),
    );
  }

  /**
   * Return label for provided status value
   * @param integer $status plan status
   * @return string
   */
  public function getAttributeLabelStatus($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }

    $labels = $this->attributeLabelsStatus();

    return isset($labels[$status]) ? $labels[$status] : '';
  }

  /**
   * Return status class name for provided status id
   * @param integer $status plan status
   * @return string
   */
  public function getStatusClass($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }

    $classes = array(
      self::STATUS_DISABLED => 'disabled',
      self::STATUS_ENABLED => 'enabled',
    );

    return (isset($classes[$status])) ? $classes[$status] : '';
  }

  /**
   * Return nameservers models assigned to this pricing plan
   * @return NameServer[]
   */
  public function getNameservers()
  {
    $pk = array();
    $pk[] = $this->defaultNameserverMaster;
    $pk[] = $this->defaultNameserverSlave1;
    if ($this->defaultNameserverSlave2) {
      $pk[] = $this->defaultNameserverSlave2;
    }
    if ($this->defaultNameserverSlave3) {
      $pk[] = $this->defaultNameserverSlave3;
    }

    return NameServer::model()->findAllByPk($pk);
  }

  /**
   * Perform search over records
   * @return CActiveDataProvider
   */
  public function search()
  {
    $criteria=new CDbCriteria;
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.type', $this->type);
    $criteria->compare('t.title', $this->title, true);
    $criteria->compare('t.domainsQty', $this->domainsQty);
    $criteria->compare('t.usersQty', $this->usersQty);
    $criteria->compare('t.nameserversQty', $this->nameserversQty);
    $criteria->compare('t.minTtl', $this->minTtl);
    $criteria->compare('t.accessApi', $this->accessApi);
    $criteria->compare('t.pricePerYear', $this->pricePerYear);
    $criteria->compare('t.pricePerMonth', $this->pricePerMonth);
    $criteria->compare('t.billing', $this->billing);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => 't.id DESC',
      )
    ));
  }
}
