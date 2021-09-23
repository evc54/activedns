<?php
/**
  Project       : ActiveDNS
  Document      : ContextHelp.php
  Document type : PHP script file
  Created at    : 01.12.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Context help storage class
*/
class ContextHelp
{
  public static function ResourceRecord($type)
  {
    switch ($type) {
      case ResourceRecord::TYPE_SOA:
        return Yii::t('contextHelp','Start of authority');
        break;

      case ResourceRecord::TYPE_A:
        return Yii::t('contextHelp','IPv4 address record for a host');
        break;

      case ResourceRecord::TYPE_AAAA:
        return Yii::t('contextHelp','IPv6 address record for a host');
        break;

      case ResourceRecord::TYPE_CNAME:
        return Yii::t('contextHelp','Canonical name (or alias) for a host');
        break;

      case ResourceRecord::TYPE_MX:
        return Yii::t('contextHelp',"Mail exchange for a domain (hostname of domain's mail server(s))");
        break;

      case ResourceRecord::TYPE_NS:
        return Yii::t('contextHelp',"Authoritative name server for a domain");
        break;

      case ResourceRecord::TYPE_SRV:
        return Yii::t('contextHelp',"Defines a service available in this zone");
        break;

      case ResourceRecord::TYPE_TXT:
        return Yii::t('contextHelp',"Text information associated with host name");
        break;
    }

    return '';
  }
}
