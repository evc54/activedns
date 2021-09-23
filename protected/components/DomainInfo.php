<?php
/**
    Project       : ActiveDNS
    Document      : DomainInfo.php
    Document type : PHP script file
    Created at    : 08.07.2012
    Author        : Eugene V Chernyshev <evc22rus@gmail.com>
    Description   : Domain information class
*/
/**
 * Collects domain information
 *
 * Usage:
 *  $domain = new DomainInfo('example.com');
 *  echo $domain->getCreateDate(); // returns date of domain creation
 */
class DomainInfo
{
  const ERROR_NO_ERROR = 0;
  const ERROR_CONNECT = 1;
  const ERROR_NO_SERVER = 2;

  private $errorCode = self::ERROR_NO_ERROR;

  /**
   * @var string Domain name with tld
   */
  private $name;
  /**
   * @var string Top level domain (TLD) name
   */
  private $tld;
  /**
   * @var boolean Is domain registered flag
   */
  private $registered = false;
  /**
   * @var array List of nameservers
   */
  private $nameServers = array();
  /**
   * @var date Date of domain creation
   */
  private $created;
  /**
   * @var date Date of domain expires
   */
  private $expires;
  /**
   * @var string Registrar name
   */
  private $registrarName;

  /**
   * @var boolean Domain info has been received
   */
  private $_infoReceived = false;

  public function __construct($domain)
  {
    $info = null;

    try {
      $info = \Iodev\Whois\Whois::create()->loadDomainInfo($domain);
    }
    catch (\Iodev\Whois\Exceptions\ConnectionException $e) {
      $this->errorCode = self::ERROR_CONNECT;
    }
    catch (\Iodev\Whois\Exceptions\ServerMismatchException $e) {
      $this->errorCode = self::ERROR_NO_SERVER;
    }

    if ($info) {
      $this->registered = true;
      $this->name = $info->getDomainName();
      $domainName = explode('.', $this->name);
      $this->tld = array_pop($domainName);
      $this->nameServers = $info->getNameServers();
      $this->created = date('Y-m-d', $info->getCreationDate());
      $this->expires = date('Y-m-d', $info->getExpirationDate());
      $this->registrarName = $info->getRegistrar();
      $this->_infoReceived = true;
    }
  }

  public function getErrorCode()
  {
    return $this->errorCode;
  }

  /**
   * Return true if info has been received from whois server
   *
   * @return boolean
   */
  public function hasReceived()
  {
    return $this->_infoReceived;
  }

  /**
   * Returns domain name
   *
   * @param boolean $withTld Return full domain name (default)
   * @return string
   */
  public function getName($withTld = true)
  {
    return $withTld ? $this->name : str_replace('.' . $this->tld,'',$this->name);
  }

  /**
   * Returns top-level domain name (TLD)
   *
   * @return string
   */
  public function getTld()
  {
    return $this->tld;
  }

  /**
   * Returns true if domain registered
   *
   * @return boolean
   */
  public function isRegistered()
  {
    return $this->registered;
  }

  /**
   * Returns nameservers list
   *
   * @return array
   */
  public function getNameServers()
  {
    return $this->nameServers;
  }

  /**
   * Returns date of domain creation
   *
   * @return date
   */
  public function getCreateDate()
  {
    return $this->created;
  }

  /**
   * Returns date of domain expiring
   *
   * @return date
   */
  public function getExpireDate()
  {
    return $this->expires;
  }

  /**
   * Returns registrar name
   *
   * @return string
   */
  public function getRegistrar()
  {
    return $this->registrarName;
  }
}
