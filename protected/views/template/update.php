<?php
/**
  Project       : ActiveDNS
  Document      : views/template/update.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Update template page
*/?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('template','{name} <small>template</small>',array('{name}'=>$model->name))?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <?php if ($model->type == Template::TYPE_PRIVATE):?>
          <li><a href="<?php echo $this->createUrl('index')?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('template','Templates management')?></a></li>
          <?php else:?>
          <li><a href="<?php echo $this->createUrl('common')?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('template','View common templates')?></a></li>
          <?php endif?>
        </ul>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li><a href="<?php echo $this->createUrl('rename',array('id'=>$model->id))?>"><s class="icon-edit icon-black"></s> <?php echo Yii::t('template','Rename template')?></a></li>
          <li><a href="<?php echo $this->createUrl('delete',array('id'=>$model->id))?>"><s class="icon-trash icon-black"></s> <?php echo Yii::t('template','Delete template')?></a></li>
        </ul>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li><a class="mass-remove disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massRemoveRR'))?>"><s class="icon-remove-circle icon-black"></s> <?php echo Yii::t('template','Delete selected records')?></a></li>
        </ul>
      </div>
    </div>
    <div class="span9">
      <div class="active-editor" id="active-editor">
        <?php $this->renderPartial('resource',array(
          'model'=>$model,
        ))?>
      </div>
    </div>
  </div>
</div>
<?php

$this->renderPartial('/domain/scripts/rr');
$this->renderPartial('/domain/scripts/mass');

$this->cs->registerScript('onSelectionChange',"
function onSelectionChange(id)
{
  var anySelected = false;
  $('.grid-view').each(function()
  {
    $(this).find('tr.read-only').removeClass('selected');
    var selected = $.fn.yiiGridView.getSelection(this.id);
    if (selected != '') {
      anySelected = true;
    }
  });
  if (anySelected) {
    $('.mass-remove').removeClass('disabled-item');
  } else {
    $('.mass-remove').addClass('disabled-item');
  }
}",CClientScript::POS_END);

$this->cs->registerScript('afterAjaxUpdate',"
function afterAjaxUpdate(target)
{
  $('#' + target + ' a[rel=tooltip]').tooltip();
  $('#' + target + ' a.modal-update').click(modalUpdateRR);
  $('#' + target + ' a.modal-remove').click(modalRemoveRR);
}",CClientScript::POS_END);
