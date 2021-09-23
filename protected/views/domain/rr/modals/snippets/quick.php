<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/snippets/host.php
  Document type : PHP script file
  Created at    : 31.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Snippet of Host input field
*/
echo CHtml::openTag('div',array('class'=>'help-block'));
  echo CHtml::tag('span',array(),Yii::t('domain','Quick select') . ':');
  echo CHtml::link('@','javascript:void(0);',array('class'=>'quick-select','rel'=>$for,'tabindex'=>-1));
  echo CHtml::link('www','javascript:void(0);',array('class'=>'quick-select','rel'=>$for,'tabindex'=>-1));
  echo CHtml::link('ftp','javascript:void(0);',array('class'=>'quick-select','rel'=>$for,'tabindex'=>-1));
  echo CHtml::link('mail','javascript:void(0);',array('class'=>'quick-select','rel'=>$for,'tabindex'=>-1));
echo CHtml::closeTag('div');
