<?php
/**
  Project       : ActiveDNS
  Document      : db.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Configuration options for Yii - database part
*/
return array(
  'components' => array(
    'db' => array(
      'connectionString'      => 'mysql:host=mariadb;dbname=activedns',
      'emulatePrepare'        => true,
      'username'              => 'root',
      'password'              => '123',
      'charset'               => 'utf8',
      'tablePrefix'           => 'ad',
      'schemaCachingDuration' => 3600,
    ),
  ),
);
