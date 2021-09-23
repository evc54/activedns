<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/ns.php
  Document type : PHP script file
  Created at    : 30.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add/update resource record type NS
*/

$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
  'quick' => true,
);

if ($rr->hasAttribute('readonly') && $rr->getAttribute('readonly')) {
  $hostAttributes = CMap::mergeArray($attributes,array('label'=>Yii::t('domain','Host'),'quick'=>false));
}
else {
  $hostAttributes = CMap::mergeArray($attributes,array('label'=>Yii::t('domain','Host'),'help'=>Yii::t('domain','Enter <strong>@</strong> for whole domain')));
}
$this->renderPartial('/domain/rr/modals/snippets/host',$hostAttributes);
$this->renderPartial('/domain/rr/modals/snippets/rdata',CMap::mergeArray($attributes,array('label'=>Yii::t('domain','Nameserver'))));
$this->renderPartial('/domain/rr/modals/snippets/ttl',$attributes);
