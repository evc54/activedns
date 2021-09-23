<?php
/**
  Project       : ActiveDNS
  Document      : models/SupportTicketReply.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support ticket replies model
*/
/**
 * @property integer $id
 * @property integer $ticketID
 * @property integer $authorID
 * @property string  $text
 * @property integer $created
 */
class SupportTicketReply extends CActiveRecord
{
  public static function model($className=__CLASS__)
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
      array('text', 'required'),
      array('text', 'filter', 'filter' => array($obj = new CHtmlPurifier(), 'purify')),
      array('ticketID,authorID,created', 'numerical', 'integerOnly' => true),
    );
  }

  public function relations()
  {
    return array(
      'ticket' => array(self::BELONGS_TO, 'SupportTicket', 'ticketID'),
      'author' => array(self::BELONGS_TO, 'User', 'authorID'),
    );
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->created = time();
      }

      return true;
    }

    return false;
  }

  public function afterSave()
  {
    parent::afterSave();
    if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN && !empty($this->ticket->author->email)) {
      $template = Yii::app()->mailer->getTemplate('supportNotify');
      if ($template !== null) {
        $ticket = $this->ticket;

        $params = array(
          '{siteName}'   => Yii::app()->name,
          '{siteUrl}'    => Yii::app()->params['siteUrl'],
          '{adminEmail}' => Yii::app()->params['adminEmail'],
          '{loginUrl}'   => Yii::app()->params['siteUrlLogin'],
          '{ticket}'     => $ticket->id,
        );

        Yii::app()->mailer->send(
          $ticket->author->email,
          $template['subject'],
          $template['body'],
          $params,
          $template['isHtml'],
          $template['attachments'],
          $template['embeddings']
        );
      }
    }
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('ticket', 'Reply #'),
      'ticketID' => Yii::t('ticket', 'Ticket #'),
      'authorID' => Yii::t('ticket', 'Author'),
      'text' => Yii::t('ticket', 'Message'),
      'created' => Yii::t('ticket', 'Created at'),
    );
  }
}
