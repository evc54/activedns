<?php
/**
  Project       : ActiveDNS
  Document      : UserIdentity.php
  Document type : PHP script file
  Created at    : 05.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : User indentity model
*/
class UserIdentity extends CUserIdentity
{
  private $_id;
  const ERROR_USER_DISABLED = 999;

  public function authenticate()
  {
    $email = trim(strtolower($this->username));
    $user = User::model()->find('LOWER(email)=?',array($email));
    if ($user === null)
      $this->errorCode = self::ERROR_USERNAME_INVALID;
    elseif ($user->status == User::USER_DISABLED)
      $this->errorCode = self::ERROR_USER_DISABLED;
    elseif(!$user->validatePassword($this->password))
      $this->errorCode = self::ERROR_PASSWORD_INVALID;
    else {
      $this->_id = $user->id;
      $this->username = $user->email;
      $this->errorCode = self::ERROR_NONE;
    }
    return $this->errorCode == self::ERROR_NONE;
  }

  public function getId()
  {
    return $this->_id;
  }
}
