<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/scripts/zone.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Zone reload scripts
*/
$this->cs->registerScript('applyAjaxZoneReload',"$('#zone-selector ul a').unbind('click').click(zoneReload);");
$this->cs->registerScript('ajaxZoneReload',"
function zoneReload(e)
{
  e.preventDefault();
  var zone = $(e.currentTarget);
  processZoneReload(zone.attr('href'));
}
function processZoneReload(url)
{
  $.ajax({dataType: 'json', url: url, cache: false, success: function(jdata)
  {
    $('#active-editor').html(jdata.editor);
    $('#soa').replaceWith(jdata.soa);
    $('#info').replaceWith(jdata.info);
    $('#zone-selector').replaceWith(jdata.selector);
    if (jdata.current) {
      $('.apply-link,.cancel-link').addClass('disabled-item');
    }
    else {
      $('.apply-link,.cancel-link').removeClass('disabled-item');
    }
    afterAjaxUpdate('active-editor');
  }});
}
",CClientScript::POS_END);
