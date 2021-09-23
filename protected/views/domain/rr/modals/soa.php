<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/soa.php
  Document type : PHP script file
  Created at    : 04.08.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to update zone' start of authority entries
*/
$error = $zone->getError('hostmaster');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Hostmaster'), 'soa-hostmaster', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('hostmaster', $zone->hostmaster, array('class'=>'input-block-level','id'=>'soa-hostmaster'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','E-mail of host master'));
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$error = $zone->getError('refresh');
$value = $zone->getSuffixValue($zone->refresh);
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Refresh'), 'soa-refresh', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('refresh', ceil($zone->refresh / $value), array('class'=>'input-small','id'=>'soa-refresh'));
    echo CHtml::dropDownList('refresh-multiplier', $value, $zone->getTtlSuffix(),array('id'=>'soa-refresh-multiplier','class'=>'span2'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Time when the slave will try to refresh a zone from the master'));
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$error = $zone->getError('retry');
$value = $zone->getSuffixValue($zone->retry);
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Retry'), 'soa-retry', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('retry', ceil($zone->retry / $value), array('class'=>'input-small','id'=>'soa-retry'));
    echo CHtml::dropDownList('retry-multiplier', $value, $zone->getTtlSuffix(),array('id'=>'soa-retry-multiplier','class'=>'span2'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Defines the time between retries if the slave (secondary) fails to contact the master when refresh (above) has expired'));
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$error = $zone->getError('expire');
$value = $zone->getSuffixValue($zone->expire);
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Expiry'), 'soa-expire', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::textField('expire', ceil($zone->expire / $value), array('class'=>'input-small','id'=>'soa-expire'));
    echo CHtml::dropDownList('expire-multiplier', $value, $zone->getTtlSuffix(),array('id'=>'soa-expire-multiplier','class'=>'span2'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Indicates when a zone data is no longer authoritative'));
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');

$error = $zone->getError('minimum');
echo CHtml::openTag('div',array('class'=>'control-group' . ($error ? ' error' : '')));
  echo CHtml::label(Yii::t('domain','Minimum'), 'soa-minimum', array('class'=>'control-label'));
  echo CHtml::openTag('div',array('class'=>'controls'));
    echo CHtml::dropDownList('minimum', $zone->minimum, Yii::app()->user->getAvailableTtl(),array('id'=>'soa-minimum','class'=>'input-medium'));
    echo CHtml::hiddenField('minimum-multiplier',1,array('id'=>'soa-minimum-multiplier'));
    echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';
    echo CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Minimum time-to-live for unspecified resource records'));
  echo CHtml::closeTag('div');
echo CHtml::closeTag('div');
