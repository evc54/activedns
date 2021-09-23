<?php
/**
  Project       : ActiveDNS
  Document      : views/template/grid.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Templates management grid
*/

$columns = array(
  array(
    'class'=>'CCheckBoxColumn',
  ),
);

if ($model->type != Template::TYPE_COMMON && Yii::app()->user->getModel()->role == User::ROLE_ADMIN) {
  $columns[] = array(
    'name'=>'idUser',
    'type'=>'html',
    'value'=>'$data->owner ? CHtml::link($data->owner->email,Yii::app()->controller->createUrl("user/update",array("id"=>$data->idUser))) : $data->idUser',
  'headerHtmlOptions'=>array('width'=>'250'),
  );
}

$columns[] = array(
  'name'=>'name',
);

$columns[] = array(
  'name'=>'recordsQty',
  'filter'=>false,
  'headerHtmlOptions'=>array('width'=>'70'),
);

$columns[] = array(
  'class'=>'CButtonColumn',
  'headerHtmlOptions'=>array('width'=>'125'),
  'template'=>'{view} {edit} {remove}',
  'buttons'=>array(
    'view'=>array(
      'label'=>'<s class="icon-eye-open"></s>',
      'url'=>'Yii::app()->controller->createUrl("view",array("id"=>$data->id))',
      'options'=>array(
        'class'=>'btn btn-mini',
        'rel'=>'tooltip',
        'title'=>Yii::t('common','View'),
      ),
      'imageUrl'=>false,
    ),
    'edit'=>array(
      'label'=>'<s class="icon-pencil"></s>',
      'url'=>'Yii::app()->controller->createUrl("update",array("id"=>$data->id))',
      'options'=>array(
        'class'=>'btn btn-primary btn-mini',
        'rel'=>'tooltip',
        'title'=>Yii::t('common','Edit'),
      ),
      'visible'=>'$data->type == Template::TYPE_PRIVATE || in_array(Yii::app()->user->getRole(),array(User::ROLE_ADMIN))',
    ),
    'remove'=>array(
      'label'=>'<s class="icon-trash"></s>',
      'url'=>'Yii::app()->controller->createUrl("delete",array("id"=>$data->id))',
      'options'=>array(
        'class'=>'btn btn-danger btn-mini',
        'rel'=>'tooltip',
        'title'=>Yii::t('common','Delete'),
      ),
      'visible'=>'$data->type == Template::TYPE_PRIVATE || in_array(Yii::app()->user->getRole(),array(User::ROLE_ADMIN))',
    ),
  ),
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
  'selectableRows'=>2,
  'selectionChanged'=>'onSelectionChange',
  'enablePagination'=>true,
  'afterAjaxUpdate'=>'afterAjaxUpdate',
  'columns'=>$columns,
));
