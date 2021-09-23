<?php
/**
  Project       : ActiveDNS
  Document      : views/account/alias.php
  Document type : PHP script file
  Created at    : 05.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameserver's aliases management grid
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
      'headerHtmlOptions'=>array('width'=>'5%'),
      'class'=>'CCheckBoxColumn',
    ),
    array(
      'headerHtmlOptions'=>array('width'=>'19%'),
      'header'=>Yii::t('nameserver','Master server'),
      'name'=>'idNameServerMaster',
      'value'=>'$data->nameServerMaster->name',
    ),
    array(
      'headerHtmlOptions'=>array('width'=>'19%'),
      'header'=>Yii::t('nameserver','Master server alias'),
      'name'=>'NameServerMasterAlias',
    ),
    array(
      'headerHtmlOptions'=>array('width'=>'19%'),
      'header'=>Yii::t('nameserver','Slave servers'),
      'type'=>'html',
      'value'=>'$data->nameServerSlave1->name . (!empty($data->nameServerSlave2) && !empty($data->nameServerSlave3) ? "<br />" . $data->nameServerSlave2->name . "<br />" . $data->nameServerSlave3->name : "")',
    ),
    array(
      'headerHtmlOptions'=>array('width'=>'19%'),
      'header'=>Yii::t('nameserver','Slave servers aliases'),
      'type'=>'html',
      'value'=>'$data->NameServerSlave1Alias . (!empty($data->NameServerSlave2Alias) && !empty($data->NameServerSlave3Alias) ? "<br />" . $data->NameServerSlave2Alias . "<br />" . $data->NameServerSlave3Alias : "")',
    ),
    array(
      'headerHtmlOptions'=>array('width'=>'7%'),
      'name'=>'load',
      'filter'=>false,
    ),
    array(
      'class'=>'CButtonColumn',
      'headerHtmlOptions'=>array('width'=>'12%'),
      'template'=>'{edit}',
      'buttons'=>array(
        'edit'=>array(
          'label'=>'<s class="icon-pencil"></s>',
          'url'=>'Yii::app()->controller->createUrl("alias",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-primary btn-mini alias-edit',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Edit'),
          ),
        ),
      ),
    ),
  ),
));
