<?php
/**
  Project       : ActiveDNS
  Document      : RemoveAccount.php
  Document type : PHP script file
  Created at    : 11.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account removal form
*/
class RemoveAccount extends CFormModel
{
  public $confirmation;

  public function rules()
  {
    return array(
      array('confirmation', 'compare', 'compareValue' => Yii::t('account', 'I confirm my account removal'), 'message' => Yii::t('account', 'Please type exactly {phrase}', array('{phrase}' => CHtml::tag('strong', array(), Yii::t('account', 'I confirm my account removal'))))),
    );
  }

  public function attributeLabels()
  {
    return array(
      'confirmation' => Yii::t('forms', 'Delete confirmation'),
    );
  }
}
