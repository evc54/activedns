<?php
/**
  Project       : ActiveDNS
  Document      : Restore.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Restore password form
*/
class Restore extends CFormModel
{
  public $email;
  public $captcha;

  public function rules()
  {
    return array(
      array('captcha', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
      array('email,captcha', 'required'),
      array('email', 'email', 'allowEmpty' => false, 'allowName' => false),
      array('email', 'validateEmail'),
    );
  }

  public function validateEmail($attribute, $params)
  {
    if (!$this->hasErrors()) {
      if (!User::model()->exists("email=:email", array(':email' => $this->email))) {
        $this->addError('email', Yii::t('error', 'E-mail you entered wasn\'t found!'));
      }
    }
  }

  public function attributeLabels()
  {
    return array(
      'email'   => Yii::t('forms', 'E-mail'),
      'captcha' => Yii::t('forms', 'Verification code'),
      'button'  => Yii::t('forms', 'Restore'),
    );
  }
}
