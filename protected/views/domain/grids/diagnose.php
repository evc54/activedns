<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/grids/diagnose.php
  Document type : PHP script file
  Created at    : 30.09.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain alerts diagnose grid
*/

$columns = array();

$columns[] = array(
  'name'=>'name',
  'headerHtmlOptions'=>array('width'=>'200'),
);

$columns[] = array(
  'class'=>'CButtonColumn',
  'headerHtmlOptions'=>array('width'=>'80'),
  'template'=>'{edit}',
  'buttons'=>array(
    'edit'=>array(
      'label'=>Yii::t('common','Edit'),
      'url'=>'Yii::app()->controller->createUrl("update",array("id"=>$data->id))',
      'options'=>array(
        'class'=>'btn btn-primary btn-mini',
      ),
    ),
  ),
);

$columns[] = array(
  'name'=>'status',
  'type'=>'raw',
  'value'=>'Yii::app()->Controller->renderPartial("labels/status",array("model"=>$data,"noTip"=>true),true)',
  'headerHtmlOptions'=>array('width'=>'100'),
);

if (Yii::app()->user->getRole() == User::ROLE_ADMIN) {
  $columns[] = array(
    'header'=>Yii::t('domain','Owner'),
    'name'=>'idUser',
    'type'=>'raw',
    'value'=>'$data->owner ? CHtml::link($data->owner->email,Yii::app()->controller->createUrl("user/update",array("id"=>$data->idUser))) : $data->idUser',
    'headerHtmlOptions'=>array('width'=>'120'),
  );
}

$columns[] = array(
  'header'=>Yii::t('alerts','Appeared'),
  'name'=>'appeared',
  'value'=>'$data->lastAlert ? Yii::app()->format->formatDatetime($data->lastAlert->create) : Yii::t("common","Unknown")',
  'headerHtmlOptions'=>array('width'=>'150'),
);

$columns[] = array(
  'name'=>'lastAlert.alert',
  'value'=>'($data->status != Domain::DOMAIN_WAITING) && $data->lastAlert ? Yii::t("alerts",$data->lastAlert->alert) : Yii::t("common","None")',
);

$columns[] = array(
  'header'=>Yii::t('alerts','Solution'),
  'type'=>'html',
  'value'=>'$data->status != Domain::DOMAIN_WAITING ? $data->lastAlert->getSolution() : Yii::t("alerts","Please wait while system checks domain state")',
);

$this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'grid' . get_class($model),
  'type'=>'striped',
  'dataProvider'=>$model->alerts(),
  'template'=>"{items}{pager}",
  'filter'=>null,
  'selectableRows'=>0,
  'enablePagination'=>true,
  'emptyText'=>Yii::t('alerts','No alerts found'),
  'columns'=>$columns,
));
