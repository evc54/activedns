<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/a.php
  Document type : PHP script file
  Created at    : 30.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add/update resource record type A
*/
$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
  'quick' => true,
);

$this->renderPartial('/domain/rr/modals/snippets/host', $attributes);
$this->renderPartial('/domain/rr/modals/snippets/rdata', CMap::mergeArray($attributes, array('label' => Yii::t('domain', 'IPv4 address'), 'help' => Yii::t('domain', 'Address must be provided in IPv4 format (1.2.3.4)'))));
$this->renderPartial('/domain/rr/modals/snippets/ttl', $attributes);
