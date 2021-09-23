<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/snippets/host.php
  Document type : PHP script file
  Created at    : 31.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Snippet of Host input field
*/

$error = $rr->getError('host');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(empty($label) ? Yii::t('domain','Host name') : $label, $id . '-host', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    $htmlOptions = array('class'=>'input-large','id'=>$id . '-host');
    if ($rr->hasAttribute('readonly') && $rr->getAttribute('readonly')) {
      $htmlOptions['readonly'] = 'readonly';
    }
    echo CHtml::textField('host', $rr->host, $htmlOptions);
    echo $error ? CHtml::tag('div',array('class'=>'help-block'),$error) : '';
    if (isset($quick) && $quick) {
      $this->renderPartial('/domain/rr/modals/snippets/quick',array('for'=>$id . '-host'));
    }
    echo empty($help) ? '' : CHtml::tag('p',array('class'=>'help-block'),$help);
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
