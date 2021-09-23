<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/aaaa.php
  Document type : PHP script file
  Created at    : 30.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add resource record type AAAA
*/

$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
  'quick' => true,
);

$this->renderPartial('/domain/rr/modals/snippets/host', $attributes);
$this->renderPartial('/domain/rr/modals/snippets/rdata', CMap::mergeArray($attributes, array('label' => Yii::t('domain', 'IPv6 address'), 'help' => Yii::t('domain', 'Address must be provided in IPv6 format (2001:db8::1)'))));
$this->renderPartial('/domain/rr/modals/snippets/ttl', $attributes);
