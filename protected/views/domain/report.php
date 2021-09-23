<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/report.php
  Document type : PHP script file
  Created at    : 06.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain add report
*/?>
<div class="container">
  <div class="row">
    <div class="span7 offset3">
      <h3><?php echo Yii::t('domain','Operation progress')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span6 offset3">
      <table class="table striped" id="process-table">
        <thead>
          <tr>
            <th><?php echo Yii::t('domain','Domain name')?></th>
            <th width="40"></th>
            <th width="200"><?php echo Yii::t('domain','Result')?></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($domains as $domain):?>
          <tr data-status="waiting"
            data-domain-name="<?php echo $domain?>"
            data-check-services="<?php echo $services ? 1 : 0?>"
            data-apply-templates="<?php echo $templates?>"
            data-nameservers="<?php echo empty($nameservers) ? '0' : $nameservers?>">
            <td><?php echo $domain?></td>
            <td><div style="width: 20px; height: 20px;" class="processing"></div></td>
            <td class="result"><?php echo Yii::t('domain','Waiting')?></td>
          </tr>
        <?php endforeach?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row fade" id="return-back">
    <div class="span6 offset3">
      <a href="<?php echo $this->createUrl('index')?>" class="btn"><s class="icon-chevron-left icon-black"></s> <?php echo Yii::t('domain','Return back')?></a>
    </div>
  </div>
</div>
<?php $this->cs->registerScript('checkFunction',"
function nextDomain()
{
  var next = $('#process-table tbody').find('tr[data-status=waiting]:first');
  if (next.length > 0) {
    next.find('.processing').addClass('grid-view-loading');
    var data = {
      domain: next.data('domain-name'),
      services: next.data('check-services'),
      templates: next.data('apply-templates'),
      nameservers: next.data('nameservers')
    };
    $.ajax({data: data, type: 'post', dataType: 'json', cache: false, success: function(jdata)
    {
      next.find('.processing').removeClass('grid-view-loading');
      next.attr('data-status',jdata.class).addClass(jdata.class);
      next.find('.result').text(jdata.result);
      setTimeout(nextDomain,100);
    }});
  }
  else {
    $('#return-back').addClass('in');
  }
}
",CClientScript::POS_END)?>
<?php $this->cs->registerScript('runDomainCheck',"
setTimeout(nextDomain,100);
")?>
