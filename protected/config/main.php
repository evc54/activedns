<?php
/**
  Project       : ActiveDNS
  Document      : config/main.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Configuration options for Yii - main part
*/
return CMap::mergeArray(
  CMap::mergeArray(
    array(
      'basePath' => dirname(dirname(__FILE__)),
      'runtimePath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'runtime',
      'name' => 'ACTIVEDNS',
      'theme' => 'bootstrap',
      'preload' => array(
        'log',
        'bootstrap',
      ),
      'import' => array(
        'application.models.*',
        'application.forms.*',
        'application.components.*',
      ),
      'components' => array(
        'cache' => array(
          'class' => 'CFileCache',
        ),
        'user' => array(
          'autoRenewCookie' => true,
          'allowAutoLogin' => true,
          'class' => 'WebUser',
        ),
        'authManager' => array(
          'class' => 'PhpAuthManager',
          'defaultRoles' => array('guest'),
        ),
        'urlManager' => array(
          'urlFormat' => 'path',
          'rules' => array(
            '/'=>'site/index',
            '<action:(pricing|signin|signup|signout|restore|confirm|message|terms|contact)>' => 'site/<action>',
            'domain/ajax/<ajax:\w+>/<id:\d+>' => 'domain/ajax',
            'domain/ajax/<ajax:\w+>/<domain:\d+>/<type:\w+>' => 'domain/ajax',
            'domain/ajax/<ajax:\w+>' => 'domain/ajax',
            '<controller:\w+>' => '<controller>/index',
            '<controller:\w+>/<id:\d+>' => '<controller>/view',
            '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
            '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
          ),
          'showScriptName' => false,
        ),
        'bootstrap' => array(
          'class' => 'ext.bootstrap.components.Bootstrap',
          'coreCss' => true,
          'responsiveCss' => true,
          'yiiCss' => true,
          'enableJS' => true,
        ),
        'errorHandler' => array(
          'errorAction' => 'site/error',
        ),
        'log' => require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log.php'),
        'mailer' => require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mailer.php')
      ),
      'params' => require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'params.php'),
    ),
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'db.php')
  ),
  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'local.php')
);
