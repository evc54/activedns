<?php
/**
  Project       : ActiveDNS
  Document      : index.php
  Document type : PHP script file
  Created at    : 26.09.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Console Command Runner
*/

defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once(implode(DIRECTORY_SEPARATOR, array(
  __DIR__,
  'vendor',
  'autoload.php',
)));

Yii::createConsoleApplication(implode(DIRECTORY_SEPARATOR, array(
  __DIR__,
  'protected',
  'config',
  'console.php',
)))->run();
