<?php

return array(
  'class' => 'CLogRouter',
  'routes' => array(
    array(
      'class' => 'CFileLogRoute',
      'levels' => 'error',
    ),
    array(
      'class'      => 'CFileLogRoute',
      'levels'     => 'info',
      'categories' => array('domain'),
      'logFile'    => 'domain.log',
    ),
  ),
);
