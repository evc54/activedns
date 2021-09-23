<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/scripts/rr.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Resource records management scripts
*/
$this->cs->registerScript('createRR',"
function modalCreateRR(e)
{
  e.preventDefault();
  e.stopPropagation();
  var url = $(e.currentTarget).prop('href');
  $.ajax({url: url, type: 'get', dataType: 'json', cache: false, success: function(jdata)
  {
    if (typeof jdata.error == 'undefined') {
      var modal = bmCreateRR(jdata.title, jdata.content, createRR);
      $(modal).data('url',url);
    }
    else {
      bmAlert(jdata.title, jdata.content);
    }
  }});
}
function createRR(e)
{
  e.preventDefault();
  e.stopPropagation();
  var modal = $('#modal-create');
  $('.errorSummary,span.help-inline',modal).fadeOut('fast',function() { $(this).remove(); });
  $('.control-group',modal).removeClass('error');
  var data = $('*[id^=modal-create-]',modal).serialize();
  var url = $(modal).data('url');
  $.ajax({url: url, type: 'post', dataType: 'json', data: data, cache: false, success: function(jdata)
  {
    if (jdata.reload) {
      processZoneReload(jdata.url);
    }
    if (jdata.success) {
      $(modal).modal('hide');
      $.fn.yiiGridView.update(jdata.grid);
    }
    if (jdata.error) {
      $(modal).find('.modal-body').html(jdata.content);
    }
  }});
}",CClientScript::POS_END);

$this->cs->registerScript('updateRR',"
function modalUpdateRR(e)
{
  e.preventDefault();
  e.stopPropagation();
  var url = $(e.currentTarget).prop('href');
  $.ajax({url: url, type: 'get', dataType: 'json', cache: false, success: function(jdata)
  {
    var modal = bmUpdateRR(jdata.title, jdata.content, updateRR)
    $(modal).data('url',url);
  }});
}
function updateRR(e)
{
  e.preventDefault();
  var modal = $('#modal-update');
  var data = $('*[id^=modal-update-]',modal).serialize();
  var url = $(modal).data('url');
  $.ajax({url: url, type: 'post', dataType: 'json', data: data, cache: false, success: function(jdata)
  {
    if (jdata.reload) {
      processZoneReload(jdata.url);
    }
    if (jdata.success) {
      $(modal).modal('hide');
      $.fn.yiiGridView.update(jdata.grid);
    }
    if (jdata.error) $(modal).find('.modal-body').html(jdata.content);
  }});
}",CClientScript::POS_END);

$this->cs->registerScript('removeRR',"
function modalRemoveRR(e)
{
  e.preventDefault();
  var url = $(e.currentTarget).prop('href');
  var modal = bmConfirm('" . Yii::t('domain','Confirm resource record removal') . "','" . Yii::t('domain','Are you sure to remove resource record?') . "',function(e)
  {
    e.preventDefault();
    e.stopPropagation();
    $(e.currentTarget).button('disable');
    $.ajax({url: url, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      if (jdata.reload) {
        processZoneReload(jdata.url);
      }
      $(modal).modal('hide');
      if (jdata.success) {
        $.fn.yiiGridView.update(jdata.grid);
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

$this->cs->registerScript('setUpdateRR',"$('body').on('click','.active-editor a.modal-create',modalCreateRR);");

$this->cs->registerScript('quickSelectLinks',"
$(document).on('click','.modal a.quick-select',function(e)
{
  e.preventDefault();
  var self = $(this);
  $('#' + self.attr('rel')).val(self.text());
});
");
