<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/srv.php
  Document type : PHP script file
  Created at    : 30.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add/update resource record type SRV
*/

$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
  'quick' => true,
);

$this->renderPartial('/domain/rr/modals/snippets/host',CMap::mergeArray($attributes,array('label'=>Yii::t('domain','Service'),'help'=>Yii::t('domain','Symbolic name of service'))));

$error = $rr->getError('proto');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Protocol'), $id . '-proto', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::dropDownList('proto', empty($rr) ? '' : $rr->proto, array(ResourceRecord::PROTO_TCP=>ResourceRecord::PROTO_TCP,ResourceRecord::PROTO_UDP=>ResourceRecord::PROTO_UDP), array('class'=>'input-large','id'=>$id . '-proto'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$error = $rr->getError('suffix');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Service name suffix'), $id . '-suffix', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('suffix', empty($rr) ? '' : $rr->suffix, array('class'=>'input-large','id'=>$id . '-suffix'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$this->renderPartial('/domain/rr/modals/snippets/priority',CMap::mergeArray($attributes,array('help'=>Yii::t('domain','Numerical unsigned value, less has greater priority'))));

$error = $rr->getError('weight');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Weight'), $id . '-weight', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('weight', empty($rr) ? '' : $rr->weight, array('class'=>'input-large','id'=>$id . '-weight'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Relative weight for records with the same priority'));
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$error = $rr->getError('port');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','TCP/UDP Port'), $id . '-port', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('port', empty($rr) ? '' : $rr->port, array('class'=>'input-large','id'=>$id . '-port'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$error = $rr->getError('port');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Target host'), $id . '-target', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('target', empty($rr) ? '' : $rr->target, array('class'=>'input-large','id'=>$id . '-target'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$this->renderPartial('/domain/rr/modals/snippets/ttl',$attributes);
