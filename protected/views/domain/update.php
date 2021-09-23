<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/update.php
  Document type : PHP script file
  Created at    : 01.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain update page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('domain','{domain} <small>zone editor</small>',array('{domain}'=>$model->name))?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li><a href="<?php echo $this->createUrl('index')?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('domain','Domains management')?></a></li>
        </ul>
        <?php if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN):?>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('domain','Owner')?></li>
          <li><a href="<?php echo $this->createUrl('user/update',array('id'=>$model->idUser))?>"><?php echo $model->owner ? $model->owner->email : $model->idUser?></a></li>
        </ul>
        <?php endif?>
        <?php $this->renderPartial('selectors/zone',array(
          'model'=>$model,
          'zone'=>$zone,
          'ajax'=>'editor',
        ))?>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
          <li><a class="apply-link<?php if ($zone->id == $model->idZoneCurrent):?> disabled-item<?php endif?>" href="<?php echo $this->createUrl('apply',array('id'=>$model->id))?>"><s class="icon-share icon-black"></s> <?php echo Yii::t('domain','Apply zone update')?></a></li>
          <li><a class="cancel-link <?php if ($zone->id == $model->idZoneCurrent):?> disabled-item<?php endif?>" href="<?php echo $this->createUrl('cancel',array('id'=>$model->id))?>"><s class="icon-ban-circle icon-black"></s> <?php echo Yii::t('domain','Cancel zone update')?></a></li>
          <li><a href="<?php echo $this->createUrl('transfer',array('id'=>$model->id,'inner'=>true))?>"><s class="icon-exchange icon-black"></s> <?php echo Yii::t('domain','Configure zone transfer')?></a></li>
          <li><a href="<?php echo $this->createUrl('export',array('id'=>$zone->id))?>"><s class="icon-download icon-black"></s> <?php echo Yii::t('domain','Export zone file')?></a></li>
          <li><a href="<?php echo $this->createUrl('import',array('id'=>$model->id))?>"><s class="icon-upload icon-black"></s> <?php echo Yii::t('domain','Import zone file')?></a></li>
        </ul>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('common','mass actions')?></li>
          <li><a class="mass-remove disabled-item" href="<?php echo $this->createUrl('ajax',array('ajax'=>'massRemoveRR'))?>"><s class="icon-remove-circle icon-black"></s> <?php echo Yii::t('domain','Remove Resource Records')?></a></li>
        </ul>
        <ul class="nav nav-list">
          <li class="nav-header"><?php echo Yii::t('domain','domain actions')?></li>
          <?php if (NameServerAlias::model()->filterByUser($model->idUser)->count()):?>
          <li><a href="<?php echo $this->createUrl('nameserver',array('id'=>$model->id,'inner'=>true))?>"><s class="icon-hdd icon-black"></s> <?php echo Yii::t('domain','Change nameservers')?></a></li>
          <?php endif?>
          <li><a href="<?php echo $this->createUrl('delete',array('id'=>$model->id,'inner'=>true))?>"><s class="icon-trash icon-black"></s> <?php echo Yii::t('domain','Delete domain')?></a></li>
          <?php if ($model->status == Domain::DOMAIN_DISABLED):?>
          <li><a href="<?php echo $this->createUrl('enable',array('id'=>$model->id,'inner'=>true))?>"><s class="icon-ok-sign icon-black"></s> <?php echo Yii::t('domain','Enable domain')?></a></li>
          <?php endif?>
          <?php if ($model->status != Domain::DOMAIN_DISABLED):?>
          <li><a href="<?php echo $this->createUrl('disable',array('id'=>$model->id,'inner'=>true))?>"><s class="icon-off icon-black"></s> <?php echo Yii::t('domain','Disable domain')?></a></li>
          <?php endif?>
          <li><a id="update-domain-info" href="<?php echo $this->createUrl('check',array('id'=>$model->id))?>"<?php if (!$model->allowAutoCheck):?> class="disabled-item"<?php endif?>><s class="icon-globe icon-black"></s> <?php echo Yii::t('domain','Refresh domain info')?></a></li>
          <?php if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN):?>
          <li><a href="<?php echo $this->createUrl('replicate',array('id'=>$model->id))?>"><s class="icon-refresh icon-black"></s> <?php echo Yii::t('domain','Force domain replication')?></a></li>
          <li><a href="<?php echo $this->createUrl('client',array('id'=>$model->id))?>"><s class="icon-retweet icon-black"></s> <?php echo Yii::t('domain','Transfer domain to another client')?></a></li>
          <?php endif?>
        </ul>
      </div>
      <?php $this->renderPartial('soa',array(
        'zone'=>$zone,
      ))?>
      <?php $this->renderPartial('info',array(
        'model'=>$model,
        'zone'=>$zone,
      ))?>
    </div>
    <div class="span9">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
      <div class="active-editor" id="active-editor">
        <?php $this->renderPartial('rr/resource',array(
          'model'=>$model,
          'zone'=>$zone,
        ))?>
      </div>
    </div>
  </div>
</div>
<?php $this->renderPartial('scripts/zone')?>
<?php $this->renderPartial('scripts/rr')?>
<?php $this->renderPartial('scripts/mass')?>
<?php $this->renderPartial('/snippets/datepicker')?>
<?php
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
  $('#' + target + ' a[rel=popover]').popover();
  $('#' + target + ' a[rel=tooltip]').tooltip();
  $('#' + target + ' a.modal-update').click(modalUpdateRR);
  $('#' + target + ' a.modal-remove').click(modalRemoveRR);
  $('#soa a.soa-update').unbind('click').click(modalUpdateSOA);
  $('#info a.info-update').unbind('click').click(modalUpdateInfo);
  $('#' + target + ' tr.read-only').click(function(e){e.stopPropagation();});
  $('#zone-selector ul a').unbind('click').click(zoneReload);
}",CClientScript::POS_END);

$this->cs->registerScript('updateSOA',"
function modalUpdateSOA(e)
{
  e.preventDefault();
  var url = $(e.currentTarget).prop('href');
  $.ajax({url: url, type: 'get', dataType: 'json', cache: false, success: function(jdata)
  {
    var modal = bmUpdateRR(jdata.title, jdata.content, updateSOA)
    $(modal).data('url',url);
  }});
}
function updateSOA(e)
{
  e.preventDefault();
  var modal = $('#modal-update');
  var data = $('*[id^=soa-]',modal).serialize();
  var url = $(modal).data('url');
  $.ajax({url: url, type: 'post', dataType: 'json', data: data, cache: false, success: function(jdata)
  {
    if (jdata.success) {
      $(modal).modal('hide');
      if (jdata.reload) {
        processZoneReload(jdata.url);
      }
      else {
        $.ajax({dataType: 'text', data: { ajax: 'soa' }, cache: false, success: function(data)
        {
          $('#soa').replaceWith($(data).filter('#soa'));
          $('a[rel=tooltip]').tooltip();
          $('a[rel=popover]').popover();
          $('#soa a.soa-update').unbind('click').click(modalUpdateSOA);
          $('#cancel').unbind('click').click(cancelUpdate);
          $('#apply').unbind('click').click(applyUpdate);
        }});
      }
    }
    if (jdata.error) {
      $(modal).find('.modal-body').html(jdata.content);
    }
  }});
}",CClientScript::POS_END);

$this->cs->registerScript('setUpdateSOA',"
$('a.soa-update').unbind('click').click(modalUpdateSOA);
",CClientScript::POS_READY);

$this->cs->registerScript('updateInfo',"
function modalUpdateInfo(e)
{
  e.preventDefault();
  var url = $(e.currentTarget).prop('href');
  $.ajax({url: url, type: 'get', dataType: 'json', cache: false, success: function(jdata)
  {
    var modal = bmUpdateRR(jdata.title, jdata.content, updateInfo)
    $(modal).data('url',url);
    $('#info-allow-auto-check',modal).unbind('click').click(function(e)
    {
      if ($(this).attr('checked')) {
        $('input:gt(0)',modal).attr('readonly','readonly');
      }
      else {
        $('input:gt(0)',modal).removeAttr('readonly');
      }
    });
    $('*[data-datepicker]',modal).datepicker({
      parse: function()
      {
        var date;
        if ((date = this.\$el.attr('rel').match(/^(\d{4,4}).?(\d{2,2}).?(\d{2,2})$/))) {
          return new Date(date[1], date[2] - 1, date[3]);
        }
        else {
          return null;
        }
      },
      format: function(date)
      {
        var result = '';

        var chunks = {
          'd': (date.getDate() < 10 ? '0' : '') + date.getDate().toString(),
          'm': (date.getMonth() < 9 ? '0' : '') + (date.getMonth() + 1).toString(),
          'y': (date.getYear() > 100 ? date.getYear() - 100 : date.getYear()).toString(),
          'Y': date.getFullYear().toString()
        };
        var format = this.\$el.data('format');
        for (i = 0, l = format.length; i < l; i++) {
          result += '' + (typeof chunks[format[i]] == 'undefined' ? format[i] : chunks[format[i]]);
        }

        return result;
      }
    });
  }});
}
function switchRel(modal)
{
  $('input[rel]',modal).each(function()
  {
    var self = $(this);
    var rel = self.attr('rel');
    self.attr('rel',self.val());
    self.val(rel);
  });
}
function updateInfo(e)
{
  e.preventDefault();
  var modal = $('#modal-update');
  $('.modal-footer .btn',modal).addClass('disabled').unbind('click').click(function(e){ e.preventDefault; e.stopPropagation(); });
  switchRel(modal);
  var data = $('*',modal).serialize();
  var allowAutoCheck = $('#info-allow-auto-check').prop('checked');
  switchRel(modal);
  var url = $(modal).data('url');
  $.ajax({url: url, type: 'post', dataType: 'json', data: data, cache: false, success: function(jdata)
  {
    if (jdata.success) {
      $(modal).modal('hide');
      $.ajax({dataType: 'text', data: { ajax: 'info' }, cache: false, success: function(data)
      {
        $('#info').replaceWith($(data).filter('#info'));
        $('a[rel=tooltip]').tooltip();
        $('a[rel=popover]').popover();
        $('#info a.info-update').unbind('click').click(modalUpdateInfo);
      }});
      if (!allowAutoCheck) {
        $('#update-domain-info').addClass('disabled-item');
      }
      else {
        $('#update-domain-info').removeClass('disabled-item');
      }
    }
    if (jdata.error) {
      $(modal).find('.modal-body').html(jdata.content);
    }
  }});
}",CClientScript::POS_END);

$this->cs->registerScript('setUpdateInfo',"
$('a.info-update').unbind('click').click(modalUpdateInfo);
",CClientScript::POS_READY);

$this->cs->registerScript('cancelUpdate',"
function cancelUpdate(e)
{
  e.preventDefault();
  var url = $(e.target).prop('href');
  bmConfirm('" . Yii::t('domain','Cancel zone update') . "','" . Yii::t('domain','Are you sure to cancel zone update?') . "',function(e)
  {
    document.location = url;
  });
}",CClientScript::POS_END);

$this->cs->registerScript('setCancelUpdate',"
$('a.cancel-link').unbind('click').click(cancelUpdate);
");

$this->cs->registerScript('applyUpdate',"
function applyUpdate(e)
{
  e.preventDefault();
  var url = $(e.target).prop('href');
  bmConfirm('" . Yii::t('domain','Apply zone update') . "','" . Yii::t('domain','Are you sure to apply zone update?') . "',function(e)
  {
    document.location = url;
  });
}",CClientScript::POS_END);

$this->cs->registerScript('setApplyUpdate',"
$('a.apply-link').unbind('click').click(applyUpdate);
");
