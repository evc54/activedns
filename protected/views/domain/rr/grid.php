<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/grid.php
  Document type : PHP script file
  Created at    : 01.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Resource record grid view
*/
?>
<h4><?php echo $type?> <small class="muted"><?php echo ContextHelp::ResourceRecord($type)?></small></h4>
<?php $this->renderPartial('/domain/rr/links/' . strtolower($type), array(
  'id' => $model->id,
))?>

<?php
// checkbox column
$checkbox = array(
  'class'=>'ECheckBoxColumn',
  'headerHtmlOptions'=>array('width'=>'15'),
  'readonly'=>'!empty($data->readonly)',
);

// host name column
$host = array(
  'name'=>'host',
  'headerHtmlOptions'=>array('width'=>'200'),
  'type'=>'html',
  'value'=>'$data->host . (empty($data->readonly) ? "" : " " . CHtml::tag("span",array("class"=>"readonly"),"read-only"))',
);

// remote data (where points to) column
$rdata = array(
  'name'=>'rdata',
);

$text = array(
  'name'=>'rdata',
  'header'=>ResourceRecord::model()->getAttributeLabel('text'),
  'htmlOptions'=>array(
    'style'=>'word-wrap: break-word;',
  ),
);

// service (for srv record) column
$service = array(
  'name'=>'host',
  'header'=>ResourceRecord::model()->getAttributeLabel('service'),
  'headerHtmlOptions'=>array('width'=>'300'),
  'value'=>'$data->host . "." . $data->proto . ($data->suffix ? "." . $data->suffix : "")',
);

// mx/srv-specific priority column
$priority = array(
  'name'=>'priority',
  'headerHtmlOptions'=>array('width'=>'50'),
);

// service name
$name = array(
  'name'=>'name',
);

// service the same priority weight
$weight = array(
  'name'=>'weight',
  'headerHtmlOptions'=>array('width'=>'50'),
);

// service target
$target = array(
  'name'=>'target',
);

// time-to-live column
$ttl = array(
  'name'=>'ttl',
  'value'=>'$data->beautyTtl()',
  'headerHtmlOptions'=>array('width'=>'70'),
);

// manage buttons

$manage = array(
  'class'=>'bootstrap.widgets.TbButtonColumn',
  'template'=>'{update} {remove}',
  'buttons'=>array(
    'update'=>array(
      'label'=>Yii::t('domain','Update Resource Record'),
      'url'=>'Yii::app()->Controller->createUrl("ajax",array("ajax"=>"ajaxActionUpdateRR","id"=>$data->id))',
      'options'=>array(
        'class'=>'modal-update',
        'data-resource-type'=>strtolower($type),
      ),
      'icon'=>'icon-pencil icon-black',
    ),
    'remove'=>array(
      'label'=>Yii::t('domain','Remove Resource Record'),
      'url'=>'Yii::app()->Controller->createUrl("ajax",array("ajax"=>"ajaxActionRemoveRR","id"=>$data->id))',
      'icon'=>'remove-circle',
      'options'=>array(
        'class'=>'modal-remove',
        'data-resource-type'=>strtolower($type),
      ),
      'icon'=>'icon-remove-circle icon-black',
      'visible'=>'empty($data->readonly)',
    ),
  ),
  'headerHtmlOptions'=>array('width'=>'50'),
);

$columns = array();

$columns[] = $checkbox;

switch ($type) {
  case ResourceRecord::TYPE_A:
  case ResourceRecord::TYPE_AAAA:
  case ResourceRecord::TYPE_CNAME:
    $columns[] = $host;
    $columns[] = $rdata;
    break;

  case ResourceRecord::TYPE_NS:
    $host['headerHtmlOptions'] = array('width'=>'100');
    $columns[] = $host;
    $columns[] = $rdata;
    break;
  case ResourceRecord::TYPE_MX:
    $host['headerHtmlOptions'] = array('width'=>'100');
    $columns[] = $host;
    $columns[] = $rdata;
    $columns[] = $priority;
    break;

  case ResourceRecord::TYPE_SRV:
    $columns[] = $service;
    $columns[] = $priority;
    $columns[] = $weight;
    $columns[] = $target;
    break;

  case ResourceRecord::TYPE_TXT:
    $columns[] = $host;
    $columns[] = $text;
    break;
}

// ttl need all types of resource records
$columns[] = $ttl;

$columns[] = $manage;

$params = array(
  'id'=>'rrgrid-' . $type,
  'htmlOptions'=>array(
    'class'=>'grid-view table-manage',
  ),
  'afterAjaxUpdate'=>'afterAjaxUpdate',
  'type'=>'striped',
  'dataProvider'=>$rr,
  'template'=>"{items}",
  'filter'=>null,
  'selectableRows'=>2,
  'selectionChanged'=>'onSelectionChange',
  'enablePagination'=>false,
  'emptyText'=>Yii::t('domain','Resource records type {type} not yet created',array('{type}'=>$type)),
  'columns'=>$columns,
);

$params['rowCssClassExpression'] = 'empty($data->readonly) ? "" : "read-only"';

$this->widget('bootstrap.widgets.TbGridView',$params);

$this->cs->registerScript('actionUpdate' . $type,"$('#rrgrid-{$type} a.modal-update').bind('click',modalUpdateRR);");
$this->cs->registerScript('actionRemove' . $type,"$('#rrgrid-{$type} a.modal-remove').bind('click',modalRemoveRR);");
