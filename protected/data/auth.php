<?php
/**
    Project       : ActiveDNS
    Document      : auth.php
    Document type : PHP script file
    Created at    : 05.01.2012
    Author        : Eugene V Chernyshev <evc22rus@gmail.com>
    Description   : Authorization rules
*/
return array(
  'guest' => array(
    'type' => CAuthItem::TYPE_ROLE,
    'description' => 'Guest',
    'bizRule' => null,
    'data' => null
  ),
  'user' => array(
    'type' => CAuthItem::TYPE_ROLE,
    'description' => 'User',
    'children' => array(
      'guest',
    ),
    'bizRule' => null,
    'data' => null
  ),
  'admin' => array(
    'type' => CAuthItem::TYPE_ROLE,
    'description' => 'Admin',
    'children' => array(
      'guest',
      'user',
    ),
    'bizRule' => null,
    'data' => null
  ),
);
