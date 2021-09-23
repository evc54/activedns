<?php
/**
  Project       : ActiveDNS
  Document      : ReloadCommand.php
  Document type : PHP script file
  Created at    : 21.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Reload data to all nameservers
*/
class ReloadCommand extends CConsoleCommand
{
  public function run($args)
  {
    $nameservers = NameServer::model()->findAll();
    foreach ($nameservers as $nameserver) {
      $nameserver->cleanZoneFiles();
    }

    $criteria = new CDbCriteria;
    $criteria->with[] = 'currentZone';
    $criteria->addInCondition('t.status', array(
      Domain::DOMAIN_WAITING,
      Domain::DOMAIN_HOSTED,
      Domain::DOMAIN_ALERT,
      Domain::DOMAIN_EXPIRED,
      Domain::DOMAIN_UPDATE
    ));
    $domains = Domain::model()->findAll($criteria);

    $updateNS = array();
    $c = count($domains);
    foreach ($domains as $i => $domain) {
      echo sprintf('[%3s%%]',round(($i + 1)/$c*100)) . ' ' . $domain->getDomainName(false) . PHP_EOL;
      if ($domain->createZoneFiles()) {
        if ($domain->currentZone) {
          foreach ($domain->currentZone->nameservers as $nameserver) {
            $updateNS[] = $nameserver->id;
          }
        }
      }
    }

    $updateNS = array_unique($updateNS);
    $nameservers = NameServer::model()->findAllByPk($updateNS);
    foreach ($nameservers as $nameserver) {
      $nameserver->regenerateZoneListFile();
      $nameserver->replicate();
    }
    foreach ($nameservers as $nameserver) {
      $nameserver->restartDaemon();
    }
  }
}
