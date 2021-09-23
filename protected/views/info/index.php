<?php
/**
  Project       : ActiveDNS
  Document      : views/info/index.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News management index page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('news','News <small>management</small>')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li class=""><a href="<?php echo $this->createUrl('create')?>"><s class="icon-plus-sign icon-black"></s> <?php echo Yii::t('news','Add news entry')?></a></li>
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li class=""><a class="mass-action mass-publish disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'ajaxActionMassPublish'))?>"><s class="icon-bullhorn icon-black"></s> <?php echo Yii::t('news','Publish selected')?></a></li>
          <li class=""><a class="mass-action mass-hide disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'ajaxActionMassHide'))?>"><s class="icon-eye-close icon-black"></s> <?php echo Yii::t('news','Hide selected')?></a></li>
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
  if ($(this).hasClass('mass-publish')) {
    var message = '" . Yii::t('news','Are you sure to publish {n} news entries?') . "';
  } else if ($(this).hasClass('mass-hide')) {
    var message = '" . Yii::t('news','Are you sure to hide {n} news entries?') . "';
  } else {
    var message = '" . Yii::t('news','Are you sure to delete {n} news entries?') . "';
  }
  var modal = bmConfirm('" . Yii::t('common','Mass action') . "',message.replace('{n}',selected.length),function(e)
  {
    $.ajax({url: url, data: { news: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      $.fn.yiiGridView.update(grid);
      modal.modal('hide');
      if (jdata.error) {
        bmAlert(jdata.error,jdata.message);
      }
      bmAlert(jdata.success,jdata.message);
    }});
  });
});");
