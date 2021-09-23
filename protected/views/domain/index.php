<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/index.php
  Document type : PHP script file
  Created at    : 09.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain management index page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('domain','Domains <small>management</small>')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li><a href="<?php echo $this->createUrl('add')?>"><s class="icon-plus-sign icon-black"></s> <?php echo Yii::t('domain','Add domain(s)')?></a></li>
          <li><a href="<?php echo $this->createUrl('diagnose')?>"><s class="icon-warning-sign icon-black"></s> <?php echo Yii::t('domain','Diagnose alerts')?></a></li>
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li><a class="mass-action mass-enable disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massEnableDomain'))?>"><s class="icon-play icon-black"></s> <?php echo Yii::t('common','Enable selected')?></a></li>
          <li><a class="mass-action mass-disable disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massDisableDomain'))?>"><s class="icon-off icon-black"></s> <?php echo Yii::t('common','Disable selected')?></a></li>
          <li><a class="mass-action mass-remove disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massRemoveDomain'))?>"><s class="icon-trash icon-black"></s> <?php echo Yii::t('common','Delete selected')?></a></li>
          <?php if (NameServerAlias::model()->filterByUser()->count()):?>
          <li><a class="mass-action mass-change-ns disabled-item" href="#nameservers"><s class="icon-hdd icon-black"></s> <?php echo Yii::t('domain','Change nameservers')?></a></li>
          <?php endif?>
          <li><a class="mass-action mass-apply-template disabled-item" href="#template"><s class="icon-bookmark icon-black"></s> <?php echo Yii::t('domain','Apply template to selected')?></a></li>
          <li><a class="mass-action mass-apply-zone disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massApplyZone'))?>"><s class="icon-share icon-black"></s> <?php echo Yii::t('domain','Apply zone update for selected')?></a></li>
          <?php if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN):?>
          <li><a class="mass-action mass-check disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massCheckDomain'))?>"><s class="icon-refresh icon-black"></s> <?php echo Yii::t('common','Refresh selected domains whois')?></a></li>
          <?php endif?>
          <li class="nav-header"><?php echo Yii::t('common','pagination')?></li>
          <li class="dropdown">
            <?php $this->renderPartial('snippets/page/selector',array('pageSize'=>Yii::app()->user->getState('DomainsPerPage',Domain::PAGESIZE)))?>
            <ul class="dropdown-menu">
              <?php $this->renderPartial('snippets/page/menu')?>
            </ul>
          </li>
          <li class="nav-header"><?php echo Yii::t('common','search')?></li>
          <li><a href="<?php echo $this->createUrl('search')?>"><s class="icon-search icon-black"></s> <?php echo Yii::t('domain','Search in resource records')?></a></li>
        </ul>
      </div>
    </div>
    <div class="span9">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
      <?php $this->renderPartial('grids/manage',array('model'=>$model))?>
    </div>
  </div>
</div>
<div class="modal fade" id="template-selector" data-url="<?php echo $this->createUrl('ajax',array('ajax'=>'massApplyTemplate'))?>">
  <div class="modal-header">
    <h3><?php echo Yii::t('domain','Apply template to selected')?></h3>
  </div>
  <div class="modal-body">
    <form class="form-horizontal" id="template-apply-data">
      <fieldset>
        <div class="control-group">
          <label class="control-label" for="template"><?php echo Yii::t('domain','Select template')?></label>
          <div class="controls">
            <?php echo CHtml::dropDownList('template', '', $templates, array('class'=>'span3'))?>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="priority"><?php echo Yii::t('domain','Select priority')?></label>
          <div class="controls">
            <?php echo CHtml::radioButtonList('priority', Template::PRIORITY_TEMPLATE, array(Template::PRIORITY_TEMPLATE=>Yii::t('domain','Template records has higher priority'),Template::PRIORITY_ZONE=>Yii::t('domain','Zone records has higher priority')),array('separator'=>''))?>
            <span class="help-block"><?php echo Yii::t('domain','Records with lower priority will be either replaced with records with higher priority or not changed')?></span>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <label class="checkbox"><input value="1" type="checkbox" name="apply" checked="checked"> <?php echo Yii::t('domain','Apply change of zone')?></label>
            <span class="help-block"><?php echo Yii::t('domain','Automatically applies change of zone to all selected domains')?></span>
          </div>
        </div>
      </fieldset>
    </form>
  </div>
  <div class="modal-footer">
    <a class="btn btn-primary" id="template-apply" href="#"><s class="icon-ok icon-white"></s> <?php echo Yii::t('domain','Apply')?></a>
    <a class="btn btn-link" id="template-cancel" href="#" data-dismiss="modal"><?php echo Yii::t('common','Cancel')?></a>
  </div>
</div>
<div class="modal fade" id="nameservers-selector" data-url="<?php echo $this->createUrl('ajax',array('ajax'=>'massChangeNameservers'))?>">
  <div class="modal-header">
    <h3><?php echo Yii::t('domain','Change nameservers for selected')?></h3>
  </div>
  <div class="modal-body">
    <form class="form-horizontal" id="nameservers-data">
      <fieldset>
        <div class="control-group">
          <label class="control-label" for="nameservers"><?php echo Yii::t('domain','Select nameservers')?></label>
          <div class="controls">
            <?php echo CHtml::dropDownList('nameservers','',NameServerAlias::model()->filterByUser()->getList(),array('empty'=>Yii::t('nameserver','Default nameservers'),'class'=>'span3'))?>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <label class="checkbox"><input value="1" type="checkbox" name="apply" checked="checked"> <?php echo Yii::t('domain','Apply change of zone')?></label>
            <span class="help-block"><?php echo Yii::t('domain','Automatically applies change of zone to all selected domains')?></span>
          </div>
        </div>
      </fieldset>
    </form>
  </div>
  <div class="modal-footer">
    <a class="btn btn-primary" id="nameservers-apply" href="#"><s class="icon-ok icon-white"></s> <?php echo Yii::t('domain','Change')?></a>
    <a class="btn btn-link" id="nameservers-cancel" href="#" data-dismiss="modal"><?php echo Yii::t('common','Cancel')?></a>
  </div>
</div>
<?php $this->cs->registerScriptFile($this->scriptUrl('filter-clear-fields'))?>
<?php $this->cs->registerScript(get_class($model) . '_SetFilterClearFields',"setClearFields();")?>
<?php $this->cs->registerScript(get_class($model) . '_SetTooltips',"setToolTip();")?>
<?php $this->cs->registerScript(get_class($model) . '_SetRefresh',"setRefresh();")?>
<?php $this->cs->registerScript(get_class($model) . '_SetStatistic',"setStatisticWidget();")?>
<?php $this->cs->registerScript('onSelectionChange',"
function onSelectionChange(id)
{
  var grid = 'grid" . get_class($model) . "';

  $('#' + grid).find('tr.read-only').removeClass('selected');
  var selected = $.fn.yiiGridView.getSelection(grid);
  if (selected != '') {
    $('.mass-action').removeClass('disabled-item');
  }
  else {
    $('.mass-action').addClass('disabled-item');
  }
}",CClientScript::POS_END)?>
<?php $this->cs->registerScript(get_class($model) . '_ModalMassAction',"
$('a.mass-apply-template').click(function(e)
{
  e.preventDefault();
  if ($(this).hasClass('disabled-item')) {
    return false;
  }
  var modal = $('#template-selector');
  modal.modal('show');
});
$('a.mass-change-ns').click(function(e)
{
  e.preventDefault();
  if ($(this).hasClass('disabled-item')) {
    return false;
  }
  var modal = $('#nameservers-selector');
  modal.modal('show');
});
$('tr.read-only').click(function(e)
{
  e.stopPropagation();
});
$('#template-apply').click(function(e)
{
  var grid = 'grid" . get_class($model) . "';
  var modal = $('#template-selector');
  var selected = $.fn.yiiGridView.getSelection(grid);
  if (!selected.length) {
    bmAlert('" . Yii::t('error','Error') . "','" . Yii::t('error','Nothing selected!') . "');
    modal.modal('hide');
    return false;
  }
  var data = $('#template-apply-data').serializeArray();
  for (i in selected) {
    data.push({ name: 'domains[]', value: selected[i] });
  }
  var button = this;
  $(button).addClass('disabled').prop('disabled',true);
  $('#template-cancel',modal).addClass('disabled').prop('disabled',true);
  $.ajax({ url: modal.data('url'), data: data, type: 'post', dataType: 'json', cache: false, success: function(jdata)
  {
    $.fn.yiiGridView.update(grid);
    modal.modal('hide');
    $(button).removeClass('disabled').prop('disabled',false);
    $('#template-cancel',modal).removeClass('disabled').prop('disabled',false);
    bmAlert(jdata.success,jdata.message);
  }});
});
$('#nameservers-apply').click(function(e)
{
  var grid = 'grid" . get_class($model) . "';
  var modal = $('#nameservers-selector');
  var selected = $.fn.yiiGridView.getSelection(grid);
  if (!selected.length) {
    bmAlert('" . Yii::t('error','Error') . "','" . Yii::t('error','Nothing selected!') . "');
    modal.modal('hide');
    return false;
  }
  var data = $('#nameservers-data').serializeArray();
  for (i in selected) {
    data.push({ name: 'domains[]', value: selected[i] });
  }
  var button = this;
  $(button).addClass('disabled').prop('disabled',true);
  $('#nameservers-cancel',modal).addClass('disabled').prop('disabled',true);
  $.ajax({ url: modal.data('url'), data: data, type: 'post', dataType: 'json', cache: false, success: function(jdata)
  {
    $.fn.yiiGridView.update(grid);
    modal.modal('hide');
    $(button).removeClass('disabled').prop('disabled',false);
    $('#nameservers-cancel',modal).removeClass('disabled').prop('disabled',false);
    bmAlert(jdata.success,jdata.message);
  }});
});
$('a.mass-enable,a.mass-disable,a.mass-remove,a.mass-check,a.mass-apply-zone').click(function(e)
{
  e.preventDefault();
  if ($(this).hasClass('disabled-item')) {
    return false;
  }
  var grid = 'grid" . get_class($model) . "';
  var action = '';
  if ($(this).hasClass('mass-enable')) {
    var message = '" . Yii::t('domain','Are you sure to enable {n} domain(s)?') . "';
  }
  else if ($(this).hasClass('mass-disable')) {
    var message = '" . Yii::t('domain','Are you sure to disable {n} domain(s)?') . "';
  }
  else if ($(this).hasClass('mass-check')) {
    var message = '" . Yii::t('domain','Are you sure to check {n} domain(s)?') . "';
  }
  else if ($(this).hasClass('mass-apply-zone')) {
    var message = '" . Yii::t('domain','Are you sure to apply new zone for {n} domain(s)?') . "';
  }
  else {
    var message = '" . Yii::t('domain','Are you sure to delete {n} domain(s)?') . "';
  }
  var url = $(e.target).prop('href');
  var selected = $.fn.yiiGridView.getSelection(grid);
  if (!selected.length) {
    bmAlert('" . Yii::t('error','Error') . "','" . Yii::t('error','Nothing selected!') . "');
    return false;
  }
  var modal = bmConfirm('" . Yii::t('common','Mass action') . "',message.replace('{n}',selected.length),function(e)
  {
    $('.modal-footer .btn,.modal-header a.close',modal).attr('disabled','disabled').on('click',function(e){ e.preventDefault(); e.stopPropagation(); });
    $.ajax({url: url, data: { domains: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      $.fn.yiiGridView.update(grid);
      modal.modal('hide');
      bmAlert(jdata.success,jdata.message);
    }});
  });
});")?>
<?php $this->cs->registerScript(get_class($model) . '_AfterAjaxUpdate',"
function setToolTip()
{
  $('*[rel=tooltip]').tooltip();
}
function afterAjaxUpdate(target, data)
{
  $('#' + target + ' tr.read-only').click(function(e)
  {
    e.stopPropagation();
  });
  $('#' + target + ' .manage-buttons > .btn').click(function(e){ e.stopPropagation(); });
  setToolTip();
  setRefresh();
  setStatisticWidget();
  setClearFields();
  onSelectionChange(target);
}
",CClientScript::POS_END)?>
<?php $this->cs->registerScript(get_class($model) . '_SetRefreshButton',"
function setRefresh()
{
  $('.table-refresh').unbind('click').bind('click',function()
  {
    $.fn.yiiGridView.update('grid" . get_class($model) . "');
  });
}
",CClientScript::POS_END)?>
<?php $this->cs->registerScript(get_class($model) . '_SetStatisticWidget',"
function setStatisticWidget()
{
  $('.sparklines').sparkline('html', { enableTagOptions: true, width: 80, height: 32 });
}
",CClientScript::POS_END)?>
<?php $this->cs->registerScript('manageButtonsBehavior',"$('.table-manage .manage-buttons > .btn').click(function(e){ e.stopPropagation(); });")?>
