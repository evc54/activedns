<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/transfer.php
  Document type : PHP script file
  Created at    : 01.06.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Configure zone transfers page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('domain','{domain} <small>zone transfer configuration</small>',array('{domain}'=>$model->name))?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li><a href="<?php echo $this->createUrl('update',array('id'=>$model->id))?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('domain','Back to zone editor')?></a></li>
        </ul>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li><a class="add-transfer-entry" href="<?php echo $this->createUrl('ajax',array('id'=>$model->id,'ajax'=>'createTransferEntry'))?>"><s class="icon-plus-sign icon-black"></s> <?php echo Yii::t('domain','Add zone transfer entry')?></a></li>
          <li><a href="<?php echo $this->createUrl('replicate',array('id'=>$model->id))?>"><s class="icon-ok-circle icon-black"></s> <?php echo Yii::t('domain','Apply configuration to domain')?></a></li>
        </ul>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li><a class="mass-remove disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massRemoveEntry'))?>"><s class="icon-remove-circle icon-black"></s> <?php echo Yii::t('domain','Remove zone transfer entries')?></a></li>
        </ul>
      </div>
    </div>
    <div class="span9">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
      <div class="active-editor" id="active-editor">
        <?php $this->renderPartial('grids/transfer',array(
          'model'=>$model,
        ))?>
      </div>
    </div>
  </div>
</div>
<?php
$this->cs->registerScript('onSelectionChange',"
function onSelectionChange(id)
{
  var anySelected = false;
  $('.grid-view').each(function()
  {
    var selected = $.fn.yiiGridView.getSelection(this.id);
    if (selected != '') {
      anySelected = true;
    }
  });
  if (anySelected) {
    $('.mass-remove').removeClass('disabled-item');
  }
  else {
    $('.mass-remove').addClass('disabled-item');
  }
}",CClientScript::POS_END);
$this->cs->registerScript('afterAjaxUpdate',"
function afterAjaxUpdate(target)
{
  $('#' + target + ' a[rel=popover]').popover();
  $('#' + target + ' a[rel=tooltip]').tooltip();
  $('#' + target + ' a.modal-update').click(modalUpdateEntry);
  $('#' + target + ' a.modal-remove').click(modalRemoveEntry);
}",CClientScript::POS_END);

$this->cs->registerScript('massActions',"
$('a.mass-remove').unbind('click').click(function(e)
{
  e.preventDefault();
  if ($(this).hasClass('disabled-item')) {
    return false;
  }
  var url = $(e.target).prop('href');
  var selected = $.fn.yiiGridView.getSelection('grid-transfer');
  if (!selected.length) {
    bmAlert('" . Yii::t('error','Error') . "','" . Yii::t('error','Nothing selected!') . "');
    return false;
  }
  var modal = bmConfirm('" . Yii::t('domain','Remove selected entries') . "','" . Yii::t('domain','Are you sure to remove selected zone transfer entries?') . " (' + selected.length + ')',function(e)
  {
    $.ajax({url: url, data: { entries: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      $.fn.yiiGridView.update('grid-transfer');
      modal.modal('hide');
    }});
  });
});");

$this->cs->registerScript('createTransferEntry',"
function modalCreateTransferEntry(e)
{
  e.preventDefault();
  var url = $(e.currentTarget).prop('href');
  $.ajax({url: url, type: 'get', dataType: 'json', cache: false, success: function(jdata)
  {
    var modal = bmCreateRR(jdata.title, jdata.content, createTransferEntry)
    $(modal).data('url',url);
  }});
}
function createTransferEntry(e)
{
  e.preventDefault();
  var modal = $('#modal-create');
  $('.errorSummary,span.help-inline',modal).fadeOut('fast',function() { $(this).remove(); });
  $('.control-group',modal).removeClass('error');
  var data = $('form',modal).serialize();
  var url = $(modal).data('url');
  $.ajax({url: url, type: 'post', dataType: 'json', data: data, cache: false, success: function(jdata)
  {
    if (jdata.success) {
      $(modal).modal('hide');
      $.fn.yiiGridView.update('grid-transfer');
    }
    if (jdata.error) {
      $(modal).find('.modal-body').html(jdata.content);
    }
  }});
}",CClientScript::POS_END);

$this->cs->registerScript('setCreateTransferEntry',"
$('a.add-transfer-entry').unbind('click').click(modalCreateTransferEntry);
",CClientScript::POS_READY);

$this->cs->registerScript('updateTransferEntry',"
function modalUpdateEntry(e)
{
  e.preventDefault();
  e.stopPropagation();
  var url = $(e.currentTarget).prop('href');
  $.ajax({url: url, type: 'get', dataType: 'json', cache: false, success: function(jdata)
  {
    var modal = bmUpdateRR(jdata.title, jdata.content, updateEntry)
    $(modal).data('url',url);
  }});
}
function updateEntry(e)
{
  e.preventDefault();
  var modal = $('#modal-update');
  var data = $('form',modal).serialize();
  var url = $(modal).data('url');
  $.ajax({url: url, type: 'post', dataType: 'json', data: data, cache: false, success: function(jdata)
  {
    if (jdata.success) {
      $(modal).modal('hide');
      $.fn.yiiGridView.update('grid-transfer');
    }
    if (jdata.error) {
      $(modal).find('.modal-body').html(jdata.content);
    }
  }});
}",CClientScript::POS_END);

$this->cs->registerScript('setUpdateEntry',"$('a.modal-update').click(modalUpdateEntry);");

$this->cs->registerScript('removeTransferEntry',"
function modalRemoveEntry(e)
{
  e.preventDefault();
  e.stopPropagation();
  var url = $(e.currentTarget).prop('href');
  var modal = bmConfirm('" . Yii::t('domain','Confirm transfer entry removal') . "','" . Yii::t('domain','Are you sure to remove zone transfer entry?') . "',function(e)
  {
    e.preventDefault();
    $(e.currentTarget).button('disable');
    $.ajax({url: url, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      $(modal).modal('hide');
      if (jdata.success) {
        $.fn.yiiGridView.update('grid-transfer');
      }
      else if (jdata) {
        bmAlert(jdata.error,jdata.message);
      }
      else {
        bmAlert('" . Yii::t('error','Error') . "', '" . Yii::t('error','Unknown AJAX error') . "');
      }
    }});
  });
}",CClientScript::POS_END);

$this->cs->registerScript('setRemoveEntry',"$('a.modal-remove').click(modalRemoveEntry);");
