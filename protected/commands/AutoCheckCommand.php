<?php
/**
  Project       : ActiveDNS
  Document      : AutoCheckCommand.php
  Document type : PHP script file
  Created at    : 15.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Automatic domain information checker
*/
class AutoCheckCommand extends CConsoleCommand
{
  const MINIMAL_TIMEOUT = 7200;

  public function run($args)
  {
    $timeout = max(self::MINIMAL_TIMEOUT, intval(Yii::app()->params['autoCheckTimeout']));
    $time = time();
    $criteria = new CDbCriteria;
    $criteria->condition = "t.lastAutoCheck < " . ($time - $timeout);
    $criteria->compare('t.allowAutoCheck','>0');
    $criteria->addNotInCondition('t.status', array(Domain::DOMAIN_DISABLED, Domain::DOMAIN_REMOVE));
    $domains = Domain::model()->findAll($criteria);
    foreach ($domains as $domain) {
      $checker = new EDomainChecker($domain);
      $domain->lastAutoCheck = time();
      $domain->save(false);
      $checker = null;
    }
  }
}
