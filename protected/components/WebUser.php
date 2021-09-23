<?php
/**
  Project       : ActiveDNS
  Document      : WebUser.php
  Document type : PHP script file
  Created at    : 05.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Webuser model
*/
class WebUser extends CWebUser
{
  private $_model;
  private $_lastSupportSeen = null;
  private $_lastEventSeen = null;

  public $loginUrl = array('/site/signin');

  public function getAttribute($attribute)
  {
    $model = $this->getModel();
    if ($model === null) return null;
    return $model->hasAttribute($attribute)?$model->getAttribute($attribute):'';
  }

  public function getRole()
  {
    return $this->getAttribute('role');
  }

  public function getRoleName()
  {
    $role = $this->getRole();

    switch ($role) {
      case User::ROLE_ADMIN:
        return 'admin';
      case User::ROLE_USER:
        return 'user';
    }

    return 'guest';
  }

  public function getStatus()
  {
    return $this->getAttribute('status');
  }

  public function getName()
  {
    return $this->getAttribute('realname');
  }

  public function getEmail()
  {
    return $this->getAttribute('email');
  }

  public function getModel()
  {
    if ($this->_model === null) {
      $this->loadUser($this->id);
    }

    return $this->_model;
  }

  protected function loadUser($__id=null)
  {
    if ($this->_model === null) {
      if ($__id !== null) {
        $this->_model = User::model()->findByPk($__id);
      }
    }

    return $this->_model;
  }

  public function setSupportSeen($timestamp)
  {
    $model = $this->getModel();
    if ($model->lastSupportSeen < $timestamp) {
      $model->lastSupportSeen = intval($timestamp);
      $model->save(false);
    }
  }

  public function getSupportSeen()
  {
    if ($this->_lastSupportSeen === null) {
      $model = $this->getModel();
      $this->_lastSupportSeen = $model->lastSupportSeen;
    }

    return $this->_lastSupportSeen;
  }

  public function setEventSeen($timestamp)
  {
    $model = $this->getModel();
    if ($model->lastEventSeen < $timestamp) {
      $model->lastEventSeen = intval($timestamp);
      $model->save(false);
    }
  }

  public function getEventSeen()
  {
    if ($this->_lastEventSeen === null) {
      $model = $this->getModel();
      $this->_lastEventSeen = $model->lastEventSeen;
    }

    return $this->_lastEventSeen;
  }

  public function beautyTtl($ttl)
  {
    $ttl = intval($ttl);

    if ($ttl >= 604800) {
      return Yii::t('common','{n} week|{n} weeks', array(ceil($ttl / 604800)));
    }

    if ((ceil($ttl/86400) > 0) && ($ttl % 86400) == 0) {
      return Yii::t('common','{n} day|{n} days', array(ceil($ttl / 86400)));
    }

    if ($ttl > 86400) {
      return Yii::t('common','More than a day');
    }

    if ((ceil($ttl / 3600)) && ($ttl % 3600) == 0) {
      return Yii::t('common','{n} hour|{n} hours', array(ceil($ttl / 3600)));
    }

    if ($ttl > 3600) {
      return Yii::t('common','More than a hour');
    }

    if ((ceil($ttl / 60)) && ($ttl % 60) == 0) {
      return Yii::t('common','{n} minute|{n} minutes', array(ceil($ttl / 60)));
    }

    if ($ttl > 60) {
      return Yii::t('common','More than a minute');
    }

    return Yii::t('common','{n} second|{n} seconds', array($ttl));
  }

  public function getAvailableTtl()
  {
    $model = $this->getModel();
    $minTtl = empty($model->plan->minTtl) ? ResourceRecord::DEFAULT_TTL : $model->plan->minTtl;

    $common = array(
      604800,
      432000,
      259200,
      172800,
      86400,
      43200,
      21600,
      10800,
      7200,
      3600,
      1800,
      900,
      600,
      300,
      60,
      30,
      15,
      10,
      5,
      1,
    );

    $available = array();
    foreach ($common as $ttl) {
      if ($minTtl <= $ttl) {
        $available[$ttl] = $this->beautyTtl($ttl);
      }
    }

    return $available;
  }
}
