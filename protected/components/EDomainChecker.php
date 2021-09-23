<?php
/**
  Project       : ActiveDNS
  Document      : EDomainChecker.php
  Document type : PHP script file
  Created at    : 27.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain checker wrapper class
*/
class EDomainChecker
{
  private $_domain = null;
  private $_isRegistered = false;
  private $_wrongNameServers = false;
  private $_wrongAliases = false;

  public function __construct($domain,$manual = false,$force = false)
  {
    $this->_domain = $domain;
    $this->_isRegistered = !$domain->emptyDomainInfo();
    if (!$manual) {
      if (!$force &&
        $this->_isRegistered &&
        !in_array($domain->status,array(Domain::DOMAIN_WAITING,Domain::DOMAIN_EXPIRED,Domain::DOMAIN_ALERT,)) &&
        strtotime($domain->expire) > time() + User::EXPIRING_DOMAINS_FORECAST
      ) {
        $this->short();
      }
      else {
        $this->long();
      }
    }
  }

  public function long()
  {
    $this->info();
    $this->short();
  }

  public function short()
  {
    if ($this->isRegistered()) {
      $this->ns();
    }
    elseif ($this->_domain->getLevel() > 2) {
      $this->_domain->allowAutoCheck = false;
      if ($this->_domain->status != Domain::DOMAIN_DISABLED) {
        $this->_domain->status = Domain::DOMAIN_HOSTED;
      }
      return;
    }
    if ($this->assign()) {
      $this->alias();
    }
    $this->alert();
  }

  public function isRegistered()
  {
    return $this->_isRegistered;
  }

  public function info()
  {
    $domainName = $this->_domain->getDomainName();
    $info = new DomainInfo($domainName);

    if (!$info->hasReceived()) {
      switch ($info->getErrorCode()) {
        case DomainInfo::ERROR_NO_SERVER:
          Yii::log('Whois server for domain ' . $domainName . ' cannot be found. Disabling automatic checking.', 'info', 'domain');
          $this->_domain->allowAutoCheck = 0;
          $this->_domain->save(false);
          break;

        case DomainInfo::ERROR_CONNECT:
          Yii::log('Timed out connection with whois server for domain ' . $domainName . '. Skipping to next session.', 'info', 'domain');
          $this->_domain->lastAutoCheck = time();
          $this->_domain->save(false);
          break;
      }

      return false;
    }

    $this->_domain->setNameServers($info->getNameServers());
    $this->_domain->register = $info->getCreateDate();
    $this->_domain->renewal = $info->getCreateDate();
    $expire = $info->getExpireDate();
    if ($expire) {
      $this->_domain->expire = $expire;
    }
    $this->_domain->registrar = $info->getRegistrar();

    $this->_isRegistered = $info->isRegistered();

    return true;
  }

  public function ns()
  {
    $nameservers = array();
    $nameserversFromDNS = @dns_get_record($this->_domain->getDomainName(), DNS_NS);

    if (is_array($nameserversFromDNS)) {
      foreach ($nameserversFromDNS as $nameserver) {
        if (!empty($nameserver['target'])) {
          $nameservers[] = $nameserver['target'];
        }
      }
    }

    if ($nameservers != array()) {
      $this->_domain->setNameServers($nameservers);
    }

    return is_array($nameserversFromDNS) && count($nameserversFromDNS);
  }

  public function assign()
  {
    $assignedNameservers = $this->_domain->domainNameservers;
    foreach (array('ns1', 'ns2', 'ns3', 'ns4') as $ns) {
      if (in_array($this->_domain->getAttribute($ns),$assignedNameservers)) {
        unset($assignedNameservers[array_search($this->_domain->getAttribute($ns),$assignedNameservers)]);
      }
    }
    if (!count($assignedNameservers)) {
      if ($this->_domain->status != Domain::DOMAIN_HOSTED) {
        $this->_domain->status = Domain::DOMAIN_HOSTED;
      }
      $this->_domain->clearAlerts();
    }
    else {
      $this->_domain->status = Domain::DOMAIN_ALERT;
      $this->_wrongNameServers = true;
    }

    return $this->_domain->status == Domain::DOMAIN_HOSTED;
  }

  public function alias()
  {
    if (!empty($this->_domain->currentZone->nameserverAlias)) {
      $alias = $this->_domain->currentZone->nameserverAlias;
    }
    if (!empty($alias)) {
      $ok = true;
      $check = array();
      $check['nameServerMaster'] = 'NameServerMasterAlias';
      $check['nameServerSlave1'] = 'NameServerSlave1Alias';
      if (!empty($alias->idNameServerSlave2) && !empty($alias->idNameServerSlave3)) {
        $check['nameServerSlave2'] = 'NameServerSlave2Alias';
        $check['nameServerSlave3'] = 'NameServerSlave3Alias';
      }

      foreach ($check as $relation => $attribute) {
        if (!$ok) {
          break;
        }
        $nameserverIP = explode("\n",$alias->$relation->publicAddress);
        $aliasIPraw = dns_get_record($alias->$attribute,DNS_A);
        $aliasIP = array();
        foreach ($aliasIPraw as $raw) {
          $aliasIP[] = $raw['ip'];
        }

        $diff = array_diff($nameserverIP,$aliasIP);
        if (!empty($diff)) {
          $ok = false;
        }
      }
      
      if (!$ok) {
        $this->_domain->status = Domain::DOMAIN_ALERT;
        $this->_wrongAliases = true;
      }
    }
  }

  public function alert()
  {
    if ($this->_domain->hasExpireDate() && $this->_domain->isExpired()) {
      if (empty($this->_domain->lastAlert->type) || $this->_domain->lastAlert->type != Alert::TYPE_DOMAIN_EXPIRED) {
        $this->_domain->alert(Alert::TYPE_DOMAIN_EXPIRED,'Domain has been expired');
        $this->_domain->logEvent(DomainEvent::TYPE_DOMAIN_EXPIRED);
      }
    }
    elseif (($this->_domain->status == Domain::DOMAIN_ALERT) && !$this->isRegistered() && $this->_domain->getLevel() < 3) {
      if (empty($this->_domain->lastAlert->type) || $this->_domain->lastAlert->type != Alert::TYPE_DOMAIN_NOT_REGISTERED) {
        $this->_domain->alert(Alert::TYPE_DOMAIN_NOT_REGISTERED,'Domain is not yet registered');
      }
    }
    elseif ($this->_domain->status == Domain::DOMAIN_ALERT && $this->_wrongNameServers) {
      if (empty($this->_domain->lastAlert->type) || $this->_domain->lastAlert->type != Alert::TYPE_WRONG_NAMESERVERS) {
        $this->_domain->alert(Alert::TYPE_WRONG_NAMESERVERS,'Domain current nameservers pointed to wrong location');
      }
    }
    elseif ($this->_domain->status == Domain::DOMAIN_ALERT && $this->_wrongAliases) {
      if (empty($this->_domain->lastAlert->type) || $this->_domain->lastAlert->type != Alert::TYPE_WRONG_NAMESERVERS_ALIASES) {
        $this->_domain->alert(Alert::TYPE_WRONG_NAMESERVERS_ALIASES,"Domain nameserver's aliases pointed to wrong location");
      }
    }
  }
}
