<?php
/**
  Project       : ActiveDNS
  Document      : config/mailer.php
  Document type : PHP script file
  Created at    : 14.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Mailer configuration
 */
return array(
  'class'     => 'CRMailer',
  'mailer'    => 'mail', // mail, sendmail or smtp
  'encoding'  => '8bit',
  'fromEmail' => 'robot@activedns.net',
  'fromName'  => 'ACTIVEDNS.NET',
);
