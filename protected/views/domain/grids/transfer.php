<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/grids/transfer.php
  Document type : PHP script file
  Created at    : 01.06.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Zone transfer configuration grid
*/
$this->widget('bootstrap.widgets.TbGridView', array(
  'id'=>'grid-transfer',
  'htmlOptions'=>array(
    'class'=>'grid-view table-manage',
  ),
  'afterAjaxUpdate'=>'afterAjaxUpdate',
  'type'=>'striped',
  'dataProvider'=>$model->transfers(),
  'template'=>"{items}{pager}",
  'filter'=>null,
  'selectableRows'=>2,
  'selectionChanged'=>'onSelectionChange',
  'enablePagination'=>true,
  'enableHistory'=>true,
  'emptyText'=>Yii::t('zone','Zone transfer not yet configured'),
  'columns'=>array(
    array(
      'class'=>'CCheckBoxColumn',
      'headerHtmlOptions'=>array('width'=>'25'),
    ),
    array(
      'name'=>'address',
    ),
    array(
      'name'=>'allowNotify',
      'type'=>'boolean',
      'headerHtmlOptions'=>array('width'=>'160'),
    ),
    array(
      'name'=>'allowTransfer',
      'type'=>'boolean',
      'headerHtmlOptions'=>array('width'=>'160'),
    ),
    array(
      'class'=>'bootstrap.widgets.TbButtonColumn',
      'headerHtmlOptions'=>array('width'=>'50'),
      'htmlOptions'=>array('nowrap'=>'nowrap','class'=>'button-column'),
      'template'=>'{update} {remove}',
      'buttons'=>array(
        'update'=>array(
          'label'=>Yii::t('domain','Update zone transfer entry'),
          'url'=>'Yii::app()->Controller->createUrl("ajax",array("ajax"=>"ajaxActionUpdateTransferEntry","id"=>$data->id))',
          'options'=>array(
            'class'=>'modal-update',
          ),
          'icon'=>'icon-pencil icon-black',
        ),
        'remove'=>array(
          'label'=>Yii::t('domain','Remove zone transfer entry'),
          'url'=>'Yii::app()->Controller->createUrl("ajax",array("ajax"=>"ajaxActionRemoveTransferEntry","id"=>$data->id))',
          'icon'=>'remove-circle',
          'options'=>array(
            'class'=>'modal-remove',
          ),
          'icon'=>'icon-remove-circle icon-black',
        ),
      ),
    ),
  ),
));
