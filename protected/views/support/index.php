<?php
/**
  Project       : ActiveDNS
  Document      : views/support/index.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support ticket's management index page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('ticket','Support')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li class=""><a href="<?php echo $this->createUrl('create')?>"><s class="icon-plus-sign icon-black"></s> <?php echo Yii::t('ticket','Add ticket')?></a></li>
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li class=""><a class="mass-action mass-close disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'ajaxActionMassClose'))?>"><s class="icon-ban-circle icon-black"></s> <?php echo Yii::t('ticket','Close selected tickets')?></a></li>
          <li class="nav-header"><?php echo Yii::t('ticket','filter')?></li>
          <?php if (empty($showClosed)):?>
          <li><a href="<?php echo $this->createUrl('index',array('closed'=>1))?>"><s class="icon-ok-circle icon-black"></s> <?php echo Yii::t('ticket','Show closed tickets')?></a></li>
          <?php else:?>
          <li><a href="<?php echo $this->createUrl('index',array('closed'=>0))?>"><s class="icon-remove-circle icon-black"></s> <?php echo Yii::t('ticket','Hide closed tickets')?></a></li>
          <?php endif?>
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
  setClearFields();
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
  var message = '" . Yii::t('ticket','Are you sure to close {n} ticket(s)?') . "';
  var modal = bmConfirm('" . Yii::t('common','Mass action') . "',message.replace('{n}',selected.length),function(e)
  {
    $.ajax({url: url, data: { tickets: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
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

$this->cs->registerScriptFile($this->scriptUrl('filter-clear-fields'));
$this->cs->registerScript(get_class($model) . '_ClearFilterFields',"setClearFields();");
