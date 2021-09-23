<?php
/**
  Project       : ActiveDNS
  Document      : console.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Console application configuration file
*/
return CMap::mergeArray(
  CMap::mergeArray(
    array(
      'basePath' => dirname(dirname(__FILE__)),
      'runtimePath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'runtime',
      'name' => 'ACTIVEDNS',
      'import' => array(
        'application.models.*',
        'application.components.*',
      ),
      'preload' => array('log'),
      'components' => array(
        'log' => require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log.php'),
        'mailer' => require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mailer.php')
      ),
      'commandMap' => array(
        'migrate'          => array(
          'class'          => 'system.cli.commands.MigrateCommand',
          'migrationPath'  => 'application.migrations',
          'migrationTable' => '{{Migration}}',
          'connectionID'   => 'db',
          'templateFile'   => 'application.migrations.template',
        ),
      ),
      'params' => require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'params.php'),
    ),
    require_once('db.php')
  ),
  require_once('local.php')
);
