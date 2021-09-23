<?php

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
  'rowCssClassExpression'=>'$data->created > Yii::app()->user->getSupportSeen() || $data->replied > Yii::app()->user->getSupportSeen() ? "success" : ""',
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
      'headerHtmlOptions'=>array('width'=>'100'),
    ),
    array(
      'name'=>'subject',
    ),
    array(
      'name'=>'created',
      'filter'=>false,
      'headerHtmlOptions'=>array('width'=>'135'),
      'type'=>'html',
      'value'=>'$data->created ? Yii::app()->format->formatDatetime($data->created) : "&mdash;"',
    ),
    array(
      'name'=>'replied',
      'filter'=>false,
      'headerHtmlOptions'=>array('width'=>'135'),
      'type'=>'html',
      'value'=>'$data->replied ? Yii::app()->format->formatDatetime($data->replied) : "&mdash;"',
    ),
    array(
      'class'=>'CButtonColumn',
      'headerHtmlOptions'=>array('width'=>'125'),
      'template'=>'{view} {close}',
      'buttons'=>array(
        'view'=>array(
          'label'=>'<s class="icon-eye-open"></s>',
          'url'=>'Yii::app()->controller->createUrl("update",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-primary btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','View'),
          ),
          'imageUrl'=>false,
        ),
        'close'=>array(
          'label'=>'<s class="icon-ban-circle"></s>',
          'url'=>'Yii::app()->controller->createUrl("close",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-inverse btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Close'),
          ),
          'visible'=>'$data->status != SupportTicket::STATUS_CLOSED',
        ),
      ),
    ),
  ),
));
