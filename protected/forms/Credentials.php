<?php
/**
  Project       : ActiveDNS
  Document      : Credentials.php
  Document type : PHP script file
  Created at    : 05.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Login form credentials model
*/
class Credentials extends CFormModel
{
  public $username;
  public $password;
  public $stayLoggedIn = false;

  private $_identity;

  public function rules()
  {
    return array(
      array('username', 'required', 'message' => Yii::t('error', 'please fill <strong>e-mail</strong> field')),
      array('password', 'required', 'message' => Yii::t('error', 'please fill <strong>password</strong> field')),
      array('stayLoggedIn', 'boolean'),
      array('password', 'authenticate'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'username'     => Yii::t('forms', 'E-mail'),
      'password'     => Yii::t('forms', 'Password'),
      'stayLoggedIn' => Yii::t('forms', 'Remember me'),
    );
  }

  public function authenticate($attribute, $params)
  {
    if (!$this->hasErrors()) {
      $this->_identity = new UserIdentity($this->username, $this->password);
      if (!$this->_identity->authenticate()) {
        if ($this->_identity->errorCode == UserIdentity::ERROR_USER_DISABLED) {
          $this->addError('password', Yii::t('error', 'your account has been disabled'));
        }
        else {
          $this->addError('password', Yii::t('error', 'credentials you entered is incorrect'));
        }
      }
    }
  }

  public function login()
  {
    if ($this->_identity === null) {
      $this->_identity = new UserIdentity($this->username, $this->password);
      $this->_identity->authenticate();
    }

    if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
      $duration = 3600 * 2;
      Yii::app()->user->login($this->_identity, $this->stayLoggedIn ? $duration : 0);
      return true;
    }
    else {
      return false;
    }
  }
}
