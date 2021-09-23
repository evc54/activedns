<?php
/**
  Project       : ActiveDNS
  Document      : ReplicateCommand.php
  Document type : PHP script file
  Created at    : 20.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Replicates update to all nameservers
*/
class ReplicateCommand extends CConsoleCommand
{
  public function run($args)
  {
    $criteria = new CDbCriteria;
    $criteria->condition = 't.idZoneCurrent <> t.idZoneReplicated OR t.status = :remove';
    $criteria->params[':remove'] = Domain::DOMAIN_REMOVE;
    $domains = Domain::model()->findAll($criteria);

    $updateNS = array();
    foreach ($domains as $domain) {
      switch ($domain->status) {
        case Domain::DOMAIN_REMOVE:
        case Domain::DOMAIN_DISABLED:
          if ($domain->unlinkZoneFiles()) {
            foreach ($domain->getDomainNameservers() as $nameserverID => $nameserverName) {
              $updateNS[] = $nameserverID;
            }
            if ($domain->status == Domain::DOMAIN_REMOVE) {
              $domain->logEvent(DomainEvent::TYPE_DOMAIN_REMOVED);
              $domain->delete();
            }
            if ($domain->status == Domain::DOMAIN_DISABLED) {
              $domain->idZoneReplicated = 0;
              $domain->save();
            }
          }
          else {
            echo 'Cannot unlink zone files for domain ' . $domain->getDomainName(false) . PHP_EOL;
          }
          break;
        default:
          if ($domain->idZoneReplicated != $domain->idZoneCurrent) {
            if ($domain->createZoneFiles()) {
              foreach ($domain->getDomainNameservers() as $nameserverID => $nameserverName) {
                $updateNS[] = $nameserverID;
              }
              if (!in_array($domain->status,array(Domain::DOMAIN_ALERT))) {
                $domain->status = Domain::DOMAIN_HOSTED;
                $domain->clearAlerts();
              }
              $domain->idZoneReplicated = $domain->idZoneCurrent;
              $domain->save(false);
              $domain->logEvent(DomainEvent::TYPE_DOMAIN_UPDATED,array('{serial}' => $domain->currentZone->serial));
            }
          }
          break;
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
