<?php
/**
  Project       : ActiveDNS
  Document      : views/plan/grid.php
  Document type : PHP script file
  Created at    : 05.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plan's grid template
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
      'headerHtmlOptions'=>array('width'=>'20'),
    ),
    array(
      'name'=>'status',
      'type'=>'html',
      'value'=>'CHtml::tag("span",array("class"=>"label label-status-" . strtolower($data->getStatusClass())),$data->getAttributeLabelStatus())',
      'filter'=>$model->attributeLabelsStatus(),
      'headerHtmlOptions'=>array('width'=>'60'),
    ),
    array(
      'name'=>'type',
      'filter'=>$model->attributeLabelsType(),
      'value'=>'$data->getAttributeLabelType()',
      'headerHtmlOptions'=>array('width'=>'90'),
    ),
    array(
      'name'=>'title',
    ),
    array(
      'name'=>'pricePerYear',
      'type'=>'html',
      'value'=>'$data->pricePerYear > 0 ? CurrencyHelper::render($data->pricePerYear) : "&mdash;"',
      'header'=>Yii::t('pricingPlan','Per year'),
      'headerHtmlOptions'=>array('width'=>'60'),
      'htmlOptions'=>array('nowrap'=>'nowrap'),
    ),
    array(
      'name'=>'pricePerMonth',
      'type'=>'html',
      'value'=>'$data->pricePerMonth > 0 ? CurrencyHelper::render($data->pricePerMonth) : "&mdash;"',
      'header'=>Yii::t('pricingPlan','Per month'),
      'headerHtmlOptions'=>array('width'=>'60'),
      'htmlOptions'=>array('nowrap'=>'nowrap'),
    ),
    array(
      'name'=>'domainsQty',
      'header'=>Yii::t('pricingPlan','Domains'),
      'type'=>'html',
      'value'=>'$data->domainsQty > 0 ? CHtml::encode($data->domainsQty) : "&infin;"',
      'headerHtmlOptions'=>array('width'=>'50'),
    ),
    array(
      'name'=>'nameserversQty',
      'header'=>Yii::t('pricingPlan',"NS's"),
      'headerHtmlOptions'=>array('width'=>'30'),
    ),
    array(
      'class'=>'CButtonColumn',
      'headerHtmlOptions'=>array('width'=>'100'),
      'htmlOptions'=>array('nowrap'=>'nowrap'),
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
          'visible'=>'$data->status == PricingPlan::STATUS_DISABLED',
        ),
        'disable'=>array(
          'label'=>'<s class="icon-off"></s>',
          'url'=>'Yii::app()->controller->createUrl("disable",array("id"=>$data->id))',
          'options'=>array(
            'class'=>'btn btn-inverse btn-mini',
            'rel'=>'tooltip',
            'title'=>Yii::t('common','Disable'),
          ),
          'visible'=>'$data->status == PricingPlan::STATUS_ENABLED',
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
