<?php
/**
  Project       : ActiveDNS
  Document      : models/SupportTicket.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support tickets model
*/
/**
 * @property integer $id
 * @property integer $status
 * @property string  $subject
 * @property integer $authorID
 * @property integer $created
 * @property integer $replied
 */
class SupportTicket extends CActiveRecord
{
  const STATUS_CREATED = 1;
  const STATUS_PROCESS = 2;
  const STATUS_CLOSED = 3;

  const PAGESIZE = 30;

  /**
   * @param string $className
   * @return SupportTicket
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
      array('subject', 'required'),
      array('status,authorID,created,replied', 'numerical', 'integerOnly' => true),
      array('subject', 'length', 'max' => 255),
      array('id,authorID,subject,status,created,replied', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'replies'    => array(self::HAS_MANY, 'SupportTicketReply', 'ticketID', 'order' => 'replies.created ASC'),
      'firstReply' => array(self::HAS_ONE, 'SupportTicketReply', 'ticketID', 'order' => 'firstReply.created ASC'),
      'lastReply'  => array(self::HAS_ONE, 'SupportTicketReply', 'ticketID', 'order' => 'lastReply.created DESC'),
      'author'     => array(self::BELONGS_TO, 'User', 'authorID'),
    );
  }

  public function unseen($timestamp)
  {
    $this->getDbCriteria()->mergeWith(array(
      'condition' => 'replied > :last OR created > :last',
      'params' => array(':last' => $timestamp),
    ));

    return $this;
  }
  
  public function hideClosed()
  {
    $this->getDbCriteria()->mergeWith(array(
      'condition' => 't.status <> :closed',
      'params' => array(
        ':closed' => self::STATUS_CLOSED,
      ),
    ));

    return $this;
  }

  public function own()
  {
    if (Yii::app()->user->getAttribute('role') != User::ROLE_ADMIN) {
      $criteria = new CDbCriteria;
      $criteria->compare('authorID', Yii::app()->user->id);
      $this->getDbCriteria()->mergeWith($criteria);
    }

    return $this;
  }

  public function close()
  {
    $this->status = self::STATUS_CLOSED;

    $reply = new SupportTicketReply('create');
    $reply->text = Yii::t('ticket', 'Ticket closed');
    $reply->authorID = Yii::app()->user->id;
    $reply->ticketID = $this->id;
    $reply->save();

    return $this->save();
  }

  public function reopen()
  {
    $this->status = self::STATUS_PROCESS;

    $reply = new SupportTicketReply('create');
    $reply->text = Yii::t('ticket', 'Ticket has been reopened');
    $reply->authorID = Yii::app()->user->id;
    $reply->ticketID = $this->id;
    $reply->save();

    return $this->save();
  }

  public function process()
  {
    $this->status = self::STATUS_PROCESS;

    $reply = new SupportTicketReply('create');
    $reply->text = Yii::t('ticket', 'Ticket set in processing state');
    $reply->authorID = Yii::app()->user->id;
    $reply->ticketID = $this->id;
    $reply->save();

    return $this->save();
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->created = time();
      }
      else {
        $this->replied = time();
      }

      return true;
    }

    return false;
  }

  public function afterSave()
  {
    Yii::app()->user->setSupportSeen(max($this->created, $this->replied));
  }

  public function attributeLabels()
  {
    return array(
      'id' =>Yii::t('ticket', 'Ticket #'),
      'status' =>Yii::t('ticket', 'Status'),
      'authorID' =>Yii::t('ticket', 'Author'),
      'subject' =>Yii::t('ticket', 'Subject'),
      'created' =>Yii::t('ticket', 'Created at'),
      'replied' =>Yii::t('ticket', 'Last replied at'),
    );
  }

  public function attributeLabelsStatus()
  {
    return array(
      self::STATUS_CREATED => Yii::t('ticket', 'New'),
      self::STATUS_PROCESS => Yii::t('ticket', 'Processing'),
      self::STATUS_CLOSED  => Yii::t('ticket', 'Closed'),
    );
  }

  public function getAttributeLabelStatus($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }
    $labels = $this->attributeLabelsStatus();

    return isset($labels[$status]) ? $labels[$status] : '';
  }

  public function getStatusClass($status = null)
  {
    if ($status === null) {
      $status = $this->status;
    }

    $classes = array(
      self::STATUS_CREATED => 'created',
      self::STATUS_PROCESS => 'process',
      self::STATUS_CLOSED  => 'closed',
    );

    return (isset($classes[$status])) ? $classes[$status] : '';
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('t.authorID', $this->authorID);
    $criteria->compare('t.status', $this->status);
    $criteria->compare('t.subject', $this->subject, false);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize'=>self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.created') . ' DESC',
      ),
    ));
  }
}
