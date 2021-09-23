<?php
/**
  Project       : ActiveDNS
  Document      : Signup.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Sign up form
*/
class Signup extends CFormModel
{
  public $email;
  public $captcha;

  public function rules()
  {
    return array(
      array('email', 'required'),
      array('captcha', 'required', 'on' => 'manual'),
      array('email', 'email', 'allowEmpty' => false, 'allowName' => false),
      array('email', 'unique', 'attributeName' => 'email', 'className' => 'User', 'message' => Yii::t('error', 'This e-mail is already registered!') . ' ' . CHtml::link(Yii::t('error', 'Did you forgot the password?'), Yii::app()->Controller->createUrl('site/restore')), 'skipOnError' => true),
    );
  }

  public function attributeLabels()
  {
    return array(
      'email'   => Yii::t('forms', 'Your e-mail'),
      'captcha' => Yii::t('forms', 'Verification code'),
    );
  }
}
