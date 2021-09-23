<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/detail.php
  Document type : PHP script file
  Created at    : 06.04.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain's detail view
*/?>
<?php
$attributes = array();
$attributes[] = array('name'=>'id');
$attributes[] = array('name'=>'idUser','type'=>'html','value'=>$model->owner ? CHtml::link($model->owner->email,$this->createUrl('user/update',array('id'=>$model->idUser))): $model->idUser);
$attributes[] = array('name'=>'status','type'=>'html','value'=>CHtml::tag('span',array('class'=>'label label-' . $model->getAttributeStatusClass()),$model->getAttributeStatusLabel()));
$attributes[] = array('name'=>'name','value'=>$model->name);
$attributes[] = array('name'=>'register','type'=>'date','label'=>$this->titleLabel(Yii::t('domain','domain created at')));
$attributes[] = array('name'=>'expire','type'=>'date','label'=>$this->titleLabel(Yii::t('domain','domain expires at')));
$attributes[] = array('name'=>'registrar','label'=>$this->titleLabel(Yii::t('domain','registrar')));
$attributes[] = array('label'=>$this->titleLabel(Yii::t('domain','assigned nameservers')),'value'=>implode(', ',$model->getDomainNameservers()));

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data'=>$model,
  'attributes'=>$attributes,
))?>
