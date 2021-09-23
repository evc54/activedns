<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/txt.php
  Document type : PHP script file
  Created at    : 30.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add resource record type TXT
*/

$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
  'quick' => true,
);

$this->renderPartial('/domain/rr/modals/snippets/host',CMap::mergeArray($attributes,array('help'=>Yii::t('domain','Text entry host. Enter <strong>@</strong> for whole domain'))));
$this->renderPartial('/domain/rr/modals/snippets/rdata',CMap::mergeArray($attributes,array('label'=>Yii::t('domain','Text entry'),'help'=>Yii::t('domain','Any text data up to {n} char|Any text data up to {n} chars',array(4096)))));
$this->renderPartial('/domain/rr/modals/snippets/ttl',$attributes);
