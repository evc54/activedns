<?php
/**
  Project       : ActiveDNS
  Document      : views/nameserver/grid.php
  Document type : PHP script file
  Created at    : 20.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameserver's management grid
*/

$this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'grid' . get_class($model),
  'htmlOptions'=>array(
    'class'=>'grid-view table-manage',
  ),
  'type'=>'striped',
  'dataProvider'=>$model->search(),
  'template'=>"{items}{pager}",
  'filter'=>$model,
  'selectableRows'=>2,
  'selectionChanged'=>'onSelectionChange',
  'enablePagination'=>true,
  'afterAjaxUpdate'=>'afterAjaxUpdate',
  'columns'=>array(
    array(
      'class'=>'CCheckBoxColumn',
    ),
    array(
      'name'=>'id',
      'headerHtmlOptions'=>array('width'=>'50'),
    ),
    array(
      'name'=>'status',
      'type'=>'html',
      'value'=>'CHtml::tag("span",array("class"=>"label label-status-" . strtolower($data->getStatusClass())),$data->getAttributeLabelStatus())',
      'filter'=>$model->attributeLabelsStatus(),
      'headerHtmlOptions'=>array('width'=>'70'),
    ),
    array(
      'name'=>'type',
      'filter'=>$model->attributeLabelsType(),
      'value'=>'$data->getAttributeLabelType()',
      'headerHtmlOptions'=>array('width'=>'50'),
    ),
    array(
      'name'=>'name',
    ),
    array(
      'name'=>'idNameServerPair',
      'type'=>'html',
      'value'=>'$data->type == NameServer::TYPE_MASTER ? ($data->pairs ? implode("<br />",CHtml::listData($data->pairs,"id","name")) : "&mdash;") : ($data->master ? $data->master->name : "&mdash;")',
    ),
    array(
      'name'=>'load',
      'filter'=>false,
      'headerHtmlOptions'=>array('width'=>'50'),
    ),
    array(
      'class'=>'CButtonColumn',
      'headerHtmlOptions'=>array('width'=>'100'),
      'template'=>'{edit} {remove} {enable} {disable}',
      'buttons'=>array(
        'edit'=>array(
          'label'=>'<s class="icon-pencil"></s>',
          'url'=>'Yii::app()->controller->createUrl("update",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-primary btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Edit'),
          ),
        ),
        'enable'=>array(
          'label'=>'<s class="icon-play"></s>',
          'url'=>'Yii::app()->controller->createUrl("enable",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-success btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Enable'),
          ),
          'visible'=>'$data->status == NameServer::STATUS_DISABLED',
        ),
        'disable'=>array(
          'label'=>'<s class="icon-off"></s>',
          'url'=>'Yii::app()->controller->createUrl("disable",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-inverse btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Disable'),
          ),
          'visible'=>'$data->status == NameServer::STATUS_ENABLED',
        ),
        'remove'=>array(
          'label'=>'<s class="icon-trash"></s>',
          'url'=>'Yii::app()->controller->createUrl("delete",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-danger btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Delete'),
          ),
        ),
      ),
    ),
  ),
));
