<?php
/**
  Project       : ActiveDNS
  Document      : ChangePassword.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : User's change password form
*/
class ChangePassword extends CFormModel
{
  public $key;
  public $email;
  public $newPassword;
  public $newPasswordConfirm;
  public $captcha;

  public function rules()
  {
    return array(
      array('key,email,newPassword,newPasswordConfirm', 'required'),
      array('email', 'email', 'allowEmpty' => false, 'allowName' => false),
      array('newPassword', 'compare', 'compareAttribute' => 'newPasswordConfirm'),
      array('newPassword', 'length', 'min' => Yii::app()->params['minPasswordLength'], 'max' => Yii::app()->params['maxPasswordLength']),
      array('captcha', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
      array('email', 'exist', 'attributeName' => 'email', 'className' => 'RestoreAccess', 'criteria' => array('condition' => '`timestamp` = ' . $this->key), 'message' => Yii::t('error', 'E-mail isn\'t found!'), 'skipOnError' => true),
    );
  }

  public function attributeLabels()
  {
    return array(
      'email'              => Yii::t('forms', 'Your e-mail'),
      'newPassword'        => Yii::t('forms', 'New password'),
      'newPasswordConfirm' => Yii::t('forms', 'Repeat new password'),
      'captcha'            => Yii::t('forms', 'Verification code'),
    );
  }
}
