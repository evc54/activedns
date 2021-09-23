<?php
/**
  Project       : ActiveDNS
  Document      : views/user/grid.php
  Document type : PHP script file
  Created at    : 16.09.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : User's management grid
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
      'headerHtmlOptions'=>array('width'=>'15'),
    ),
    array(
      'name'=>'id',
      'headerHtmlOptions'=>array('width'=>'40'),
    ),
    array(
      'name'=>'status',
      'type'=>'html',
      'value'=>'CHtml::tag("span",array("class"=>"label label-status-" . strtolower($data->getStatusClass())),$data->getAttributeLabelStatus())',
      'filter'=>$model->attributeLabelsStatus(),
      'headerHtmlOptions'=>array('width'=>'65'),
    ),
    array(
      'name'=>'email',
      'type'=>'raw',
      'value'=>'CHtml::mailto($data->email)',
    ),
    array(
      'name'=>'idPricingPlan',
      'value'=>'$data->plan ? $data->plan->title : Yii::t("common","Unknown")',
      'headerHtmlOptions'=>array('width'=>'100'),
    ),
    array(
      'name'=>'paidTill',
      'type'=>'html',
      'value'=>'$data->paidTill ? Yii::app()->format->formatDate($data->paidTill) : "&mdash;"',
      'headerHtmlOptions'=>array('width'=>'75'),
    ),
    array(
      'header'=>Yii::t('user','Domains'),
      'type'=>'html',
      'name'=>'totalDomainsQty',
      'value'=>'$data->totalDomainsQty . " " . Yii::t("user","of") . " " . ($data->getMaxDomainsQty() > 0 ? $data->getMaxDomainsQty() : "&infin;")',
      'headerHtmlOptions'=>array('width'=>'70'),
      'filter'=>false,
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
        'remove'=>array(
          'label'=>'<s class="icon-trash"></s>',
          'url'=>'Yii::app()->controller->createUrl("delete",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-danger btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Delete'),
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
          'visible'=>'$data->status == User::USER_DISABLED',
        ),
        'disable'=>array(
          'label'=>'<s class="icon-off"></s>',
          'url'=>'Yii::app()->controller->createUrl("disable",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-inverse btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Disable'),
          ),
          'visible'=>'$data->status == User::USER_ENABLED',
        ),
      ),
    ),
  ),
));
