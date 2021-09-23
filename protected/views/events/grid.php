<?php
/**
  Project       : ActiveDNS
  Document      : views/events/grid.php
  Document type : PHP script file
  Created at    : 08.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Events viewer grid
*/

$columns = array(
  array(
    'name'=>'name',
    'headerHtmlOptions'=>array('width'=>'200'),
  ),
);

if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN) {
  $columns[] = array(
    'name'=>'idUser',
    'headerHtmlOptions'=>array('width'=>'200'),
    'type'=>'html',
    'value'=>'$data->owner ? CHtml::link($data->owner->email,Yii::app()->controller->createUrl("user/update",array("id"=>$data->idUser))) : $data->idUser',
  );
}

$columns[] = array(
  'name'=>'create',
  'headerHtmlOptions'=>array('width'=>'150'),
  'filter'=>false,
  'type'=>'datetime',
);

$columns[] = array(
  'name'=>'idEventType',
  'type'=>'html',
  'headerHtmlOptions'=>array('width'=>'160'),
  'filter'=>$model->attributeTypeLabels(),
  'value'=>'CHtml::tag("span",array("class"=>"label label-" . $data->getAttributeTypeClass()),$data->getAttributeTypeLabel())',
);

$columns[] = array(
  'name'=>'event',
  'filter'=>false,
  'value'=>'Yii::t("events",$data->event,$data->getParam())',
);

$this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'grid' . get_class($model),
  'htmlOptions'=>array(
    'class'=>'grid-view table-manage',
  ),
  'type'=>'striped',
  'dataProvider'=>$model->search(),
  'template'=>"{items}{pager}",
  'filter'=>$model,
  'selectableRows'=>0,
  'enablePagination'=>true,
  'emptyText'=>Yii::t('events','No events found'),
  'rowCssClassExpression'=>'$data->create > Yii::app()->user->getEventSeen() ? "success" : ""',
  'afterAjaxUpdate'=>'afterAjaxUpdate',
  'columns'=>$columns,
));
