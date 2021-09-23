<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/cname.php
  Document type : PHP script file
  Created at    : 30.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add/update resource record type CNAME
*/
$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
);

$this->renderPartial('/domain/rr/modals/snippets/host', $attributes);

$error = $rr->getError('rdata');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Canonical name'), $id . '-rdata', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    //echo CHtml::dropDownList('rdata', $rr->rdata, $model->getPointsForCname(), array('class'=>'input-large','id'=>$id . '-rdata'));
    echo CHtml::textField('rdata',$rr->rdata, array('class'=>'input-large','id'=>$id . '-rdata'));
    if ($error) {
      echo CHtml::tag('div',array('class'=>'help-block'), $error);
    }
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$this->renderPartial('/domain/rr/modals/snippets/ttl', $attributes);
