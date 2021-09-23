<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/snippets/priority.php
  Document type : PHP script file
  Created at    : 31.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Snippet of Priority input field
*/
$error = $rr->getError('priority');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(empty($label) ? Yii::t('domain','Priority') : $label, $id . '-priority', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('priority', $rr->priority, array('class'=>'input-large','id'=>$id . '-priority'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo empty($help) ? '' : CHtml::tag('p',array('class'=>'help-block'),$help);
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
