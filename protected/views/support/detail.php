<?php
/**
  Project       : ActiveDNS
  Document      : views/support/detail.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support ticket's detail view
*/

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data'=>$model,
  'attributes'=>array(
    array('name'=>'id'),
    array('name'=>'status','type'=>'html','value'=>CHtml::tag('span',array('class'=>'label label-status-' . strtolower($model->getStatusClass())), $model->getAttributeLabelStatus())),
    array('name'=>'created','type'=>'html','value'=>$model->created ? CHtml::encode(Yii::app()->format->formatDatetime($model->created)) : '&mdash;'),
    array('name'=>'subject'),
    array('name'=>'firstReply.text'),
    array('name'=>'replied','type'=>'html','value'=>$model->replied ? CHtml::encode(Yii::app()->format->formatDatetime($model->replied)) : '&mdash;'),
    array('name'=>'lastReply.text'),
  ),
));
