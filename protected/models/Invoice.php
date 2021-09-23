<?php
/**
  Project       : ActiveDNS
  Document      : models/Invoice.php
  Document type : PHP script file
  Created at    : 10.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Payment gateway invoice model
*/
/**
 * @property integer $status
 * @property integer $userID
 * @property string  $email
 * @property string  $invoiceID
 * @property string  $transactionID
 * @property float   $amount
 * @property float   $incomingAmount
 * @property string  $signature
 * @property string  $incomingSignature
 * @property integer $created
 * @property integer $completed
 * @property integer $planID
 * @property integer $billing
 */
class Invoice extends CActiveRecord
{
  const PAGESIZE = 25;

  const STATUS_WAITING = 0;
  const STATUS_SUCCESS = 1;
  const STATUS_FAIL = 2;

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
      array('userID,email,amount', 'required'),
      array('email', 'length', 'max' => 255),
      array('invoiceID,transactionID,signature,incomingSignature', 'length', 'max' => 32),
      array('paidTill', 'length', 'max' => 10),
      array('status', 'in', 'range' => array(self::STATUS_WAITING, self::STATUS_SUCCESS, self::STATUS_FAIL)),
      array('userID,created,completed', 'numerical', 'integerOnly' => true),
      array('amount,incomingAmount', 'numerical'),
      array('id,status,userID,invoiceID,amount,incomingAmount,signature,incomingSignature,created,completed', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'user' => array(self::BELONGS_TO, 'User', 'userID'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('invoice', 'ID'),
      'status' => Yii::t('invoice', 'Status'),
      'userID' => Yii::t('invoice', 'User'),
      'email' => Yii::t('invoice', 'E-mail'),
      'invoiceID' => Yii::t('invoice', 'Invoice #'),
      'transactionID' => Yii::t('invoice', 'Transaction #'),
      'amount' => Yii::t('invoice', 'Amount'),
      'incomingAmount' => Yii::t('invoice', 'Incoming amount'),
      'signature' => Yii::t('invoice', 'Payment signature'),
      'incomingSignature' => Yii::t('invoice', 'Incoming payment signature'),
      'created' => Yii::t('invoice', 'Created at'),
      'completed' => Yii::t('invoice', 'Completed at'),
      'planID' => Yii::t('invoice', 'Pricing plan'),
      'billing' => Yii::t('invoice', 'Billing cycle'),
    );
  }

  public function search()
  {
    $criteria=new CDbCriteria;
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.email', $this->email, true);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.amount', $this->amount);
    $criteria->compare('t.incomingAmount', $this->incomingAmount);
    $criteria->compare('t.invoiceID', $this->invoiceID);
    $criteria->compare('t.transactionID', $this->transactionID);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize'=>self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => 't.created DESC',
      )
    ));
  }

  public function filterUser($userID = null)
  {
    if (!$userID) {
      $userID = Yii::app()->user->id;
    }

    $criteria = new CDbCriteria;
    $criteria->compare('userID', $userID);

    $this->getDbCriteria()->mergeWith($criteria);

    return $this;
  }

  public function beforeSave()
  {
    if ($this->isNewRecord) {
      $this->created = time();
    }

    return true;
  }

  public function result($signature, $amount, $status = self::STATUS_SUCCESS)
  {
    $this->incomingAmount = $amount;
    $this->incomingSignature = $signature;
    $this->completed = time();
    $this->status = $status;

    return $this->save();
  }
}
