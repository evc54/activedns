<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/scripts/mass.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Mass actions for resource records
*/
$this->cs->registerScript('massActions',"
$('tr.read-only').unbind('click').click(function(e)
{
  e.stopPropagation();
});
$('a.mass-remove').unbind('click').click(function(e)
{
  e.preventDefault();
  if ($(this).hasClass('disabled-item')) {
    return false;
  }
  var url = $(e.target).prop('href');
  var selected = [];
  var grids = [];
  $('.grid-view').each(function()
  {
    var selection = $.fn.yiiGridView.getSelection(this.id);
    if (selection.length) grids.push(this.id);
    selected = selected.concat(selection);
  });
  if (!selected.length) {
    bmAlert('" . Yii::t('error','Error') . "','" . Yii::t('error','Nothing selected!') . "');
    return false;
  }
  var modal = bmConfirm('" . Yii::t('domain','Remove resource records') . "','" . Yii::t('domain','Are you sure to remove selected resource record(s)?') . " (' + selected.length + ')',function(e)
  {
    $.ajax({url: url, data: { rr: selected }, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      if (jdata.reload) {
        processZoneReload(jdata.url);
      }
      else {
        for (i in grids) {
          $.fn.yiiGridView.update(grids[i]);
        }
      }
      modal.modal('hide');
    }});
  });
});");
