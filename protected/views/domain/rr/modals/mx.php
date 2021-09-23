<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/mx.php
  Document type : PHP script file
  Created at    : 30.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add/update resource record type MX
*/

$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
  'quick' => true,
);

$this->renderPartial('/domain/rr/modals/snippets/host',CMap::mergeArray($attributes,array('help'=>Yii::t('domain','Mail host. Enter <strong>@</strong> for whole domain'))));
$this->renderPartial('/domain/rr/modals/snippets/priority',CMap::mergeArray($attributes,array('help'=>Yii::t('domain','Numerical unsigned value'))));
$this->renderPartial('/domain/rr/modals/snippets/rdata',CMap::mergeArray($attributes,array('label'=>Yii::t('domain','Mail server'),'help'=>Yii::t('domain','Server that handle mail for this host'))));
$this->renderPartial('/domain/rr/modals/snippets/ttl',$attributes);
