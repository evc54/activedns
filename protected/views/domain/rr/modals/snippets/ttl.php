<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/snippets/ttl.php
  Document type : PHP script file
  Created at    : 31.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Snippet of Time-To-Live input field
*/
$error = $rr->getError('ttl');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Time-To-Live'), $id . '-ttl', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::dropDownList('ttl', empty($rr->ttl) ? ResourceRecord::DEFAULT_TTL : $rr->ttl, Yii::app()->user->getAvailableTtl(), array('class'=>'input-large','id'=>$id . '-ttl'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
