<?php
/**
  Project       : ActiveDNS
  Document      : index.php
  Document type : PHP script file
  Created at    : 26.09.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Application Entry Point
*/

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require_once(implode(DIRECTORY_SEPARATOR, array(
  dirname(__DIR__),
  'vendor',
  'autoload.php',
)));

Yii::createWebApplication(implode(DIRECTORY_SEPARATOR, array(
  dirname(__DIR__),
  'protected',
  'config',
  'main.php',
)))->run();
