<?php
/**
  Project       : ActiveDNS
  Document      : views/account/nameserver.php
  Document type : PHP script file
  Created at    : 05.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameservers aliases manager
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
    </div>
  </div>
  <form class="form-horizontal">
    <div class="row">
      <div class="span12">
        <fieldset>
          <legend><?php echo Yii::t('nameserver','Assigned nameservers')?></legend>
        </fieldset>
      </div>
    </div>
    <div class="row">
      <div class="span6">
        <div class="control-group">
          <label class="control-label"><?php echo $user->getAttributeLabel('ns1')?></label>
          <div class="controls">
            <span style="font-size: 14px; display: inline-block; margin: 5px 0;">
              <?php echo $nameserversNames[$user->getAttribute('ns1')]?>
              <br />
              <?php echo str_replace(array(" ","\r\n","\r","\n","\t"),'<br />',$nameserversAddresses[$user->getAttribute('ns1')])?>
            </span>
          </div>
        </div>
      </div>
      <div class="span6">
        <div class="control-group">
          <label class="control-label"><?php echo $user->getAttributeLabel('ns2')?></label>
          <div class="controls">
            <span style="font-size: 14px; display: inline-block; margin: 5px 0;">
              <?php echo $nameserversNames[$user->getAttribute('ns2')]?>
              <br />
              <?php echo str_replace(array(" ","\r\n","\r","\n","\t"),'<br />',$nameserversAddresses[$user->getAttribute('ns2')])?>
            </span>
          </div>
        </div>
        <?php if ($user->plan->nameserversQty > 2):?>
        <?php if (!empty($nameserversNames[$user->getAttribute('ns3')])):?>
        <div class="control-group">
          <label class="control-label"><?php echo $user->getAttributeLabel('ns3')?></label>
          <div class="controls">
            <span style="font-size: 14px; display: inline-block; margin: 5px 0;">
              <?php echo $nameserversNames[$user->getAttribute('ns3')]?>
              <br />
              <?php echo str_replace(array(" ","\r\n","\r","\n","\t"),'<br />',$nameserversAddresses[$user->getAttribute('ns3')])?>
            </span>
          </div>
        </div>
        <?php endif?>
        <?php if (!empty($nameserversNames[$user->getAttribute('ns4')])):?>
        <div class="control-group">
          <label class="control-label"><?php echo $user->getAttributeLabel('ns4')?></label>
          <div class="controls">
            <span style="font-size: 14px; display: inline-block; margin: 5px 0;">
              <?php echo $nameserversNames[$user->getAttribute('ns4')]?>
              <br />
              <?php echo str_replace(array(" ","\r\n","\r","\n","\t"),'<br />',$nameserversAddresses[$user->getAttribute('ns4')])?>
            </span>
          </div>
        </div>
        <?php endif?>
        <?php endif?>
      </div>
    </div>
  </form>
  <div class="row">
    <div class="span12">
      <fieldset>
        <legend><?php echo Yii::t('nameserver',"Nameserver's aliases")?></legend>
      </fieldset>
    </div>
  </div>
  <div class="row">
    <?php if ($user->plan->type == PricingPlan::TYPE_FREE):?>
    <div class="span12">
      <span class="muted"><?php echo Yii::t('nameserver',"Nameserver's aliases is not available for your account type")?></span>
    </div>
    <?php else:?>
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li><a href="#modal-add-alias" data-toggle="modal"><s class="icon-tags icon-black"></s> <?php echo Yii::t('nameserver','Add aliases')?></a></li>
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li><a class="mass-action mass-remove disabled-item" href="<?php echo $this->createUrl('unalias')?>"><s class="icon-remove-circle icon-black"></s> <?php echo Yii::t('common','Delete selected')?></a></li>
        </ul>
      </div>
    </div>
    <div class="span9">
      <?php $this->renderPartial('alias/grid',array(
        'model'=>$model,
      ))?>
    </div>
    <?php endif?>
  </div>
</div>
<?php if ($user->plan->type != PricingPlan::TYPE_FREE):?>
<div class="modal fade" id="modal-add-alias">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo Yii::t('nameserver',"Add nameserver's aliases")?></h3>
  </div>
  <div class="modal-body">
    <?php $this->renderPartial('alias/form',array(
      'user'=>$user,
      'nameserversNames'=>$nameserversNames,
      'nameserversAddresses'=>$nameserversAddresses,
      'aliasSource'=>array(
        1=>$user->ns1,
        2=>$user->ns2,
        3=>$user->ns3,
        4=>$user->ns4,
      ),
    ))?>
  </div>
  <div class="modal-footer">
    <a id="add-alias" href="#" class="btn btn-primary" data-loading-text="<?php echo Yii::t('common','Processing...')?>"><?php echo Yii::t('common','Add')?></a>
    <a href="#" class="btn" data-dismiss="modal"><?php echo Yii::t('common','Cancel')?></a>
  </div>
</div>
<div class="modal fade" id="modal-update-alias">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo Yii::t('nameserver',"Update nameserver's aliases")?></h3>
  </div>
  <div class="modal-body">
  </div>
  <div class="modal-footer">
    <a id="update-alias" href="#" class="btn btn-primary" data-loading-text="<?php echo Yii::t('common','Processing...')?>"><?php echo Yii::t('common','Save')?></a>
    <a href="#" class="btn" data-dismiss="modal"><?php echo Yii::t('common','Cancel')?></a>
  </div>
</div>
<?php $this->cs->registerScriptFile($this->scriptUrl('dialogs'))?>
<?php if (Yii::app()->language != 'en') {
  $this->cs->registerScriptFile($this->scriptUrl('dialogs-locale-' . Yii::app()->language));
}?>
<?php $this->cs->registerScriptFile($this->scriptUrl('filter-clear-fields'))?>
<?php $this->cs->registerScript('AliasGridHandle',"
function afterAjaxUpdate(grid,data)
{
  onSelectionChange(grid);
  initAliasUpdate(grid);
  setClearFields();
}
function onSelectionChange(grid)
{
  var selected = $.fn.yiiGridView.getSelection(grid);
  if (selected != '') {
    $('.mass-action').removeClass('disabled-item');
  }
  else {
    $('.mass-action').addClass('disabled-item');
  }
}
",CClientScript::POS_END)?>
<?php $this->cs->registerScript('AddModalHandle',"
var emptyAddForm = $('#modal-add-alias .modal-body').html();
$('#modal-add-alias').on('hidden',function()
{
  $('form#form-add-alias input').val('');
  $('#modal-add-alias .modal-body').html(emptyAddForm);
});
")?>
<?php $this->cs->registerScript('UpdateModalHandleFunction',"
function initAliasUpdate(grid)
{
  var grid = $('#' + grid);
  var modal = $('#modal-update-alias');
  $('.alias-edit',grid).click(function(e)
  {
    e.preventDefault();
    var url = $(this).attr('href');
    $.ajax({
      cache: false,
      type: 'get',
      dataType: 'json',
      url: url,
      beforeSend: function(xhr,settings)
      {
        $('.modal-body',modal).empty();
        $('#update-alias').hide();
        $('.alias-edit',grid).attr('disabled','disabled');
      },
      success: function (jdata,status,xhr)
      {
        $('.modal-body',modal).html(jdata.form);
        $('#update-alias').show().attr('href',url);
      },
      error: function (xhr,status,error)
      {
        $('.modal-body',modal).html($('<div />').addClass('alert alert-error').text(error));
      },
      complete: function(xhr,status)
      {
        $('.alias-edit',grid).removeAttr('disabled');
        $(modal).modal('show');
      }
    });
  });
}
",CClientScript::POS_END)?>
<?php $this->cs->registerScript('UpdateModalHandle',"
setClearFields();
initAliasUpdate('grid" . get_class($model) . "');
$('#update-alias').click(function(e)
{
  var modal = $('#modal-update-alias');
  e.preventDefault();
  $.ajax({
    cache: false,
    type: 'post',
    url: $(this).attr('href'),
    data: $('form',modal).serialize(),
    dataType: 'json',
    beforeSend: function(xhr,settings)
    {
      $('.modal-body .alert',modal).remove();
      $('#update-alias').button('loading');
    },
    success: function (jdata,status,xhr)
    {
      if (jdata.success) {
        modal.modal('hide');
        $.fn.yiiGridView.update('grid" . get_class($model) . "');
      }
      else {
        $('.modal-body',modal).fadeOut('fast',function()
        {
          $(this).html(jdata.form).fadeIn('fast');
        });
      }
    },
    error: function (xhr,status,error)
    {
      var error = $('<div />').addClass('alert alert-error').html(error);
      $('.modal-body').prepend(error);
    },
    complete: function(xhr,status)
    {
      $('#update-alias').button('reset');
    }
  });
});
")?>
<?php $this->cs->registerScript('AddButtonHandle',"
$('#add-alias').click(function(e)
{
  e.preventDefault();
  $.ajax({
    cache: false,
    type: 'post',
    data: $('#modal-add-alias form').serialize(),
    dataType: 'json',
    beforeSend: function(xhr,settings)
    {
      $('#modal-add-alias .modal-body .alert').remove();
      $('#add-alias').button('loading');
    },
    success: function (jdata,status,xhr)
    {
      if (jdata.success) {
        $('#modal-add-alias').modal('hide');
        $.fn.yiiGridView.update('grid" . get_class($model) . "');
      }
      else {
        $('#modal-add-alias .modal-body').fadeOut('fast',function()
        {
          $(this).html(jdata.form).fadeIn('fast');
        });
      }
    },
    error: function (xhr,status,error)
    {
      var error = $('<div />').addClass('alert alert-error').html(error);
      $('#modal-add-alias .modal-body').prepend(error);
    },
    complete: function(xhr,status)
    {
      $('#add-alias').button('reset');
    }
  });
});
")?>
<?php $this->cs->registerScript(get_class($model) . '_ModalMassAction',"
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
  var message = '" . Yii::t('nameserver','Are you sure to delete {n} alias(es)?') . "';
  var modal = bmConfirm('" . Yii::t('common','Mass action') . "',message.replace('{n}',selected.length),function(e)
  {
    $.ajax({url: url, data: { aliases: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      $.fn.yiiGridView.update(grid);
      modal.modal('hide');
      if (jdata.error) {
        bmAlert(jdata.error,jdata.message);
      }
      bmAlert(jdata.success,jdata.message);
    }});
  });
});")?>
<?php endif?>
