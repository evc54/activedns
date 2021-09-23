<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/grids/expire.php
  Document type : PHP script file
  Created at    : 13.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Expiring domains grid
*/
$this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'grid' . get_class($model),
  'type'=>'striped',
  'dataProvider'=>$model->expiring(),
  'template'=>"{items}{pager}",
  'filter'=>$model,
  'selectableRows'=>0,
  'enablePagination'=>true,
  'emptyText'=>Yii::t('domain','No domains found'),
  'columns'=>array(
    array(
      'name'=>'name',
      'headerHtmlOptions'=>array('width'=>'200'),
    ),
    array(
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
    ),
    array(
      'name'=>'status',
      'type'=>'raw',
      'value'=>'Yii::app()->Controller->renderPartial("labels/status",array("model"=>$data,"noTip"=>true),true)',
      'filter'=>$model->attributeStatusLabels(),
      'headerHtmlOptions'=>array('width'=>'50'),
    ),
    array(
      'name'=>'expire',
      'headerHtmlOptions'=>array('width'=>'300'),
      'filter'=>false,
      'value'=>'$data->expire ? Yii::app()->format->formatDate($data->expire) : Yii::t("common","Unknown")',
    ),
  ),
));
