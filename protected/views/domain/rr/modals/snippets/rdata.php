<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/snippets/rdata.php
  Document type : PHP script file
  Created at    : 31.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Snippet of RData input field
*/
$error = $rr->getError('rdata');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(empty($label) ? Yii::t('domain','Address points to') : $label, $id . '-rdata', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    $htmlOptions = array('class'=>'input-large','id'=>$id . '-rdata');
    if ($rr->hasAttribute('readonly') && $rr->getAttribute('readonly')) {
      $htmlOptions['readonly'] = 'readonly';
    }
    echo CHtml::textField('rdata', empty($rr) ? '' : $rr->rdata, $htmlOptions);
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo empty($help) ? '' : CHtml::tag('p',array('class'=>'help-block'),$help);
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
