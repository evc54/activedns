<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/signin.php
  Document type : PHP script file
  Created at    : 07.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Sign in drop down menu code snippet
*/

Yii::app()->bootstrap->registerDropdown();

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'action'=>$this->createUrl('site/signin'),
  'htmlOptions'=>array(
    'id'=>'signInForm',
    'class'=>'signin',
  ),
));

  echo $form->textFieldRow($model, 'username', array('class'=>'input-large'));
  echo $form->passwordFieldRow($model, 'password', array('class'=>'input-large'));
  echo $form->checkBoxRow($model,'stayLoggedIn');
  $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'icon'=>'ok white',
    'label'=>Yii::t('site','Sign in'),
  ));

$this->endWidget();

// Fix input element click problem
$this->cs->registerScript('fixLoginDropdownDisappear',"
$('.dropdown .signin').click(function(e){e.stopPropagation()});
");
