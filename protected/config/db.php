<?php
/**
  Project       : ActiveDNS
  Document      : db.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Configuration options for Yii - database part
*/

$localFile = __DIR__ . DIRECTORY_SEPARATOR . 'db.local.php';

return file_exists($localFile) ? require($localFile) : array(
  'components' => array(
    'db' => array(
      'connectionString'      => 'mysql:host=dev-mysql-serv;dbname=dev',
      'emulatePrepare'        => true,
      'username'              => 'activedns',
      'password'              => '',
      'charset'               => 'utf8',
      'tablePrefix'           => 'ad',
      'schemaCachingDuration' => 0,
    ),
  ),
);
