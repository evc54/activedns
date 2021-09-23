<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/grids/manage.php
  Document type : PHP script file
  Created at    : 09.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain management grid
*/
if (Yii::app()->user->getRole() != User::ROLE_ADMIN) {
  $stats = array(
    'header'=>Yii::t('domain','Stats'),
    'type'=>'raw',
    'value'=>'$data->renderStats()',
    'headerHtmlOptions'=>array('width'=>'80','class'=>'statistic-cell'),
    'htmlOptions'=>array('class'=>'statistic-cell'),
  );
}
else {
  $owner = array(
    'header'=>Yii::t('domain','Owner'),
    'name'=>'idUser',
    'type'=>'raw',
    'value'=>'$data->owner ? CHtml::link($data->owner->email,Yii::app()->controller->createUrl("user/update",array("id"=>$data->idUser))) : $data->idUser',
    //'headerHtmlOptions'=>array('width'=>'150'),
  );
}
$this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'grid' . get_class($model),
  'htmlOptions'=>array(
    'class'=>'grid-view table-manage',
  ),
  'afterAjaxUpdate'=>'afterAjaxUpdate',
  'type'=>'striped',
  'dataProvider'=>$model->own()->search(),
  'template'=>"{items}{pager}",
  'filter'=>$model,
  'selectableRows'=>2,
  'selectionChanged'=>'onSelectionChange',
  'enablePagination'=>true,
  'enableHistory'=>true,
  'emptyText'=>Yii::t('domain','No domains found'),
  'rowCssClassExpression'=>'$data->readonly ? "read-only" : ""',
  'columns'=>array(
    array(
      'class'=>'ECheckBoxColumn',
      'readonly'=>'$data->readonly',
      'headerHtmlOptions'=>array('width'=>'15'),
    ),
    array(
      'name'=>'name',
      'type'=>'raw',
      'value'=>'Yii::app()->controller->renderPartial("labels/name",array("model"=>$data),true)',
    ),
    array(
      'name'=>'status',
      'filter'=>$model->attributeStatusLabels(),
      'type'=>'raw',
      'value'=>'Yii::app()->controller->renderPartial("labels/status",array("model"=>$data),true)',
      'headerHtmlOptions'=>array('width'=>'100'),
    ),
    array(
      'name'=>'expire',
      'filter'=>'',
      'headerHtmlOptions'=>array('width'=>'70'),
      'type'=>'html',
      'value'=>'$data->expire ? Yii::app()->format->formatDate($data->expire) : "&mdash;"',
    ),
    Yii::app()->user->getRole() != User::ROLE_ADMIN ? $stats : $owner,
    array(
      'class'=>'CButtonColumn',
      'headerHtmlOptions'=>array('class'=>'manage-buttons'),
      'htmlOptions'=>array('nowrap'=>'nowrap','class'=>'manage-buttons'),
      'template'=>'{edit} {enable} {disable} {remove}',
      'header'=>CHtml::tag('button',array('class'=>'pull-right btn btn-mini table-refresh'),CHtml::tag('s',array('class'=>'icon icon-refresh'),'') . ' ' . Yii::t('common','Refresh')),
      'buttons'=>array(
        'edit'=>array(
          'label'=>'<s class="icon-pencil"></s>',
          'url'=>'Yii::app()->controller->createUrl("update",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-primary btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Edit'),
          ),
          'visible'=>'$data->status != Domain::DOMAIN_REMOVE',
        ),
        'enable'=>array(
          'label'=>'<s class="icon-play"></s>',
          'url'=>'Yii::app()->controller->createUrl("enable",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-success btn-mini hidden-medium',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Enable'),
          ),
          'visible'=>'$data->status == Domain::DOMAIN_DISABLED',
        ),
        'disable'=>array(
          'label'=>'<s class="icon-off"></s>',
          'url'=>'Yii::app()->controller->createUrl("disable",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-inverse btn-mini hidden-medium',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Disable'),
          ),
          'visible'=>'$data->status != Domain::DOMAIN_DISABLED',
        ),
        'remove'=>array(
          'label'=>'<s class="icon-trash"></s>',
          'url'=>'Yii::app()->controller->createUrl("delete",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-danger btn-mini hidden-medium',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Delete'),
          ),
          'visible'=>'$data->status != Domain::DOMAIN_REMOVE',
        ),
      ),
    ),
  ),
));
