<?php
/**
  Project       : ActiveDNS
  Document      : views/info/grid.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News management grid
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
      'headerHtmlOptions'=>array('width'=>'30'),
    ),
    array(
      'name'=>'public',
      'type'=>'boolean',
      'filter'=>Yii::app()->format->booleanFormat,
      'headerHtmlOptions'=>array('width'=>'75'),
    ),
    array(
      'name'=>'idUser',
      'value'=>'$data->author ? $data->author->realname : $data->idUser',
      'headerHtmlOptions'=>array('width'=>'150'),
    ),
    array(
      'name'=>'create',
      'type'=>'datetime',
      'headerHtmlOptions'=>array('width'=>'120'),
    ),
    array(
      'name'=>'currentLanguageContent.title',
    ),
    array(
      'class'=>'CButtonColumn',
      'headerHtmlOptions'=>array('width'=>'100'),
      'template'=>'{edit} {remove} {publish} {hide}',
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
        'publish'=>array(
          'label'=>'<s class="icon-bullhorn"></s>',
          'url'=>'Yii::app()->controller->createUrl("publish",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-success btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('news','Publish'),
          ),
          'visible'=>'!$data->public',
        ),
        'hide'=>array(
          'label'=>'<s class="icon-eye-close"></s>',
          'url'=>'Yii::app()->controller->createUrl("hide",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-inverse btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('news','Hide'),
          ),
          'visible'=>'$data->public',
        ),
      ),
    ),
  ),
));
