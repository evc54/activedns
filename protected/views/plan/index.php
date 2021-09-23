<?php
/**
  Project       : ActiveDNS
  Document      : views/plan/index.php
  Document type : PHP script file
  Created at    : 05.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plan's management index page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('pricingPlan','Pricing plans <small>management</small>')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li class=""><a href="<?php echo $this->createUrl('create')?>"><s class="icon-plus-sign icon-black"></s> <?php echo Yii::t('pricingPlan','Add pricing plan')?></a></li>
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li class=""><a class="mass-action mass-enable disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'ajaxActionMassEnable'))?>"><s class="icon-play icon-black"></s> <?php echo Yii::t('common','Enable selected')?></a></li>
          <li class=""><a class="mass-action mass-disable disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'ajaxActionMassDisable'))?>"><s class="icon-off icon-black"></s> <?php echo Yii::t('common','Disable selected')?></a></li>
          <li class=""><a class="mass-action mass-remove disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'ajaxActionMassRemove'))?>"><s class="icon-trash icon-black"></s> <?php echo Yii::t('common','Delete selected')?></a></li>
        </ul>
      </div>
    </div>
    <div class="span9">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
      <?php $this->renderPartial('grid',array(
        'model'=>$model,
      ))?>
    </div>
  </div>
</div>
<?php

$this->cs->registerScript('gridFunctions',"
function onSelectionChange(id)
{
  var grid = 'grid" . get_class($model) . "';
  var selected = $.fn.yiiGridView.getSelection(grid);
  if (selected != '') {
    $('.mass-action').removeClass('disabled-item');
  } else {
    $('.mass-action').addClass('disabled-item');
  }
}
function afterAjaxUpdate(id, data)
{
  onSelectionChange(id);
}
",CClientScript::POS_END);

$this->cs->registerScript(get_class($model) . '_ModalMassAction',"
$('a.mass-action').unbind('click').bind('click',function(e)
{
  var grid = 'grid" . get_class($model) . "';
  e.preventDefault();
  if ($(this).hasClass('disabled-item')) {
    return false;
  }
  var url = $(e.target).prop('href');
  var selected = $.fn.yiiGridView.getSelection(grid);
  if (!selected.length) {
    bmAlert('" . Yii::t('error','Error') . "','" . Yii::t('error','Nothing selected!') . "');
    return false;
  }
  var action = '';
  if ($(this).hasClass('mass-enable')) {
    var message = '" . Yii::t('pricingPlan','Are you sure to enable {n} pricing plan(s)?') . "';
  } else if ($(this).hasClass('mass-disable')) {
    var message = '" . Yii::t('pricingPlan','Are you sure to disable {n} pricing plan(s)?') . "';
  } else {
    var message = '" . Yii::t('pricingPlan','Are you sure to delete {n} pricing plan(s)?') . "';
  }
  var modal = bmConfirm('" . Yii::t('common','Mass action') . "',message.replace('{n}',selected.length),function(e)
  {
    $.ajax({url: url, data: { plans: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      $.fn.yiiGridView.update(grid);
      modal.modal('hide');
      if (jdata.error) {
        bmAlert(jdata.error,jdata.message);
      }
      else {
        bmAlert(jdata.success,jdata.message);
      }
    }});
  });
});");
