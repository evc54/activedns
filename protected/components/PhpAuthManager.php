<?php
/**
  Project       : ActiveDNS
  Document      : PhpAuthManager.php
  Document type : PHP script file
  Created at    : 05.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Authentication manager
*/
class PhpAuthManager extends CPhpAuthManager
{
  public function init()
  {
    parent::init();

    if (!Yii::app()->user->isGuest) {
        $this->assign(Yii::app()->user->getRoleName(), Yii::app()->user->id);
    }

    return true;
  }
}
