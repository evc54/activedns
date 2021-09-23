<?php
/**
  Project       : ActiveDNS
  Document      : views/template/index.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Templates management index page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <?php if ($this->action->id == 'index'):?>
      <h3><?php echo Yii::t('template','Templates <small>management</small>')?></h3>
      <?php else:?>
      <h3><?php echo Yii::t('template','<small>View</small> common templates')?></h3>
      <?php endif?>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <?php if ($this->action->id == 'index'):?>
          <li><a href="<?php echo $this->createUrl('common')?>"><s class="icon-bookmark-empty icon-black"></s> <?php echo Yii::t('template','View common templates')?> (<?php echo Template::model()->common()->count()?>)</a></li>
          <?php else:?>
          <li><a href="<?php echo $this->createUrl('index')?>"><s class="icon-bookmark icon-black"></s> <?php echo Yii::t('template','Templates management')?></a></li>
          <?php endif?>
          <?php if ($this->action->id == 'index' || in_array(Yii::app()->user->getRole(),array(User::ROLE_ADMIN))):?>
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li><a href="<?php echo $this->createUrl('create')?>"><s class="icon-plus-sign icon-black"></s> <?php echo Yii::t('template','Add template')?></a></li>
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li><a class="mass-action mass-remove disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'ajaxActionMassRemove'))?>"><s class="icon-trash icon-black"></s> <?php echo Yii::t('common','Delete selected')?></a></li>
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
  }
  else {
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
$('a.mass-action').click(function(e)
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
  var message = '" . Yii::t('template','Are you sure to delete {n} template(s)?') . "';
  var modal = bmConfirm('" . Yii::t('common','Mass action') . "',message.replace('{n}',selected.length),function(e)
  {
    $.ajax({url: url, data: { templates: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
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
