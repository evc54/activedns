<?php
/**
  Project       : ActiveDNS
  Document      : views/panel/index.php
  Document type : PHP script file
  Created at    : 09.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Authorized user dashboard
*/
?>
<?php $this->cs->registerScriptFile('https://www.google.com/jsapi')?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
    </div>
  </div>
  <div class="row">
    <div class="span8">
      <div class="row-fluid">
        <div class="span12">
          <h3><?php echo Yii::t('dashboard','Account summary')?></h3>
        </div>
      </div>
      <div class="row-fluid account-summary">
        <div class="span9">
          <div class="row-fluid">
            <div class="span6">
              <div class="text-center">
                <div class="info-bl domain">
                  <h4><?php echo Yii::t('dashboard','Domains')?></h4>
                  <div class="indicator">
                    <span style="width: <?php echo $maxDomainsQty > 0 ? round($this->getCounter('totalDomainsQty')/$maxDomainsQty*100) : 0?>%;"></span>
                  </div>
                  <div class="numerical">
                    <span class="has"><?php echo $this->getCounter('totalDomainsQty')?></span>
                    <span><?php echo Yii::t('dashboard','of')?></span>
                    <span class="max"><?php echo $maxDomainsQty > 0 ? $maxDomainsQty : '&infin;'?></span>
                  </div>
                  <div class="info-bl-arrow"></div>
                </div>
              </div>
              <div class="info-bl-link"><a href="<?php echo $this->createUrl('domain/add')?>"><?php echo Yii::t('dashboard','Add domains')?></a></div>
            </div>
            <div class="span6">
              <div class="info-bl domain-active">
                <h4><?php echo Yii::t('dashboard','Active domains')?></h4>
                <div class="indicator">
                  <span style="width: <?php echo $this->emptyCounter('totalDomainsQty') ? 0 : round($activeDomainsQty/$this->getCounter('totalDomainsQty')*100)?>%;"></span>
                </div>
                <div class="numerical">
                  <span class="has"><?php echo $activeDomainsQty?></span>
                  <span><?php echo Yii::t('dashboard','of')?></span>
                  <span class="max"><?php echo $this->getCounter('totalDomainsQty')?></span>
                </div>
                <div class="info-bl-arrow"></div>
              </div>
              <div class="info-bl-link"><a href="<?php echo $this->createUrl('domain/index')?>"><?php echo Yii::t('dashboard','Manage your domains')?></a></div>
            </div>
          </div>
        </div>
        <div class="span3">
          <div class="info-bl<?php echo empty($totalAlertsQty) ? ' domain-ok' : ' domain-alert'?>">
            <h4><?php echo Yii::t('dashboard','Alerts')?></h4>
            <div class="numerical">
              <span class="has"><?php echo $totalAlertsQty?></span>
            </div>
            <div class="info-bl-arrow"></div>
          </div>
          <div class="info-bl-link"><a href="<?php echo $this->createUrl('domain/diagnose')?>"><?php echo Yii::t('dashboard','Diagnose alerts')?></a></div>
        </div>
      </div>
    </div>
    <div class="span4">
      <a class="more-news" href="<?php echo $this->createUrl('news/index')?>"><?php echo Yii::t('dashboard','More news')?> <s class="icon-caret-right"></s></a>
      <h3><?php echo Yii::t('dashboard','Latest news')?></h3>
      <?php $this->widget('bootstrap.widgets.TbListView',array(
        'id'=>'news',
        'dataProvider'=>$news->latest(),
        'itemView'=>'news',
        'template'=>'{items}',
        'emptyText'=>CHtml::tag('span',array('class'=>'muted'),Yii::t('news','News not yet available')),
      ))?>
    </div>
  </div>
  <div class="row">
    <div class="span7">
      <h3><?php echo Yii::t('dashboard','Queries statistics')?></h3>
      <span class="period-selector">
        <?php echo Yii::t('dashboard','for a')?>
        <span class="btn-group">
          <a href="#" data-var="day" class="btn btn-small active"><?php echo Yii::t('dashboard','Last day')?></a>
          <a href="#" data-var="week" class="btn btn-small"><?php echo Yii::t('dashboard','Week')?></a>
          <a href="#" data-var="month" class="btn btn-small"><?php echo Yii::t('dashboard','Month')?></a>
          <a href="#" data-var="year" class="btn btn-small"><?php echo Yii::t('dashboard','Year')?></a>
        </span>
      </span>
      <div id="q-hits"><div class="chart-loading"><?php echo Yii::t('dashboard','Loading chart data...')?></div></div>
    </div>
    <div class="span5">
      <h3><?php echo Yii::t('dashboard','Domains expiring soon')?></h3>
      <table class="table table-condensed">
        <thead>
          <tr>
            <th width="70%"><?php echo Yii::t('dashboard','Domain')?></th>
            <th><?php echo Yii::t('dashboard','Expiration date')?></th>
          </tr>
        </thead>
        <tbody>
        <?php if ($expiringDomains['qty'] == 0):?>
          <tr>
            <td colspan="2"><span class="muted"><?php echo Yii::t('dashboard','No expiring domains found')?></span></td>
          </tr>
        <?php endif?>
        <?php foreach ($expiringDomains['expiring'] as $domain):?>
          <tr>
            <td><a href="<?php echo $this->createUrl('/domain/update',array('id'=>$domain->id))?>"><?php echo $domain->getDomainName(false)?></a></td>
            <td><span<?php echo strtotime($domain->expire) < time() ? ' class="text-error"' : ''?>><?php echo $domain->expire ? Yii::app()->format->formatDate($domain->expire) : Yii::t('common','Unknown')?></span></td>
          </tr>
        <?php endforeach?>
        </tbody>
      </table>
      <?php if ($expiringDomains['qty'] > count($expiringDomains['expiring'])):?>
      <a href="<?php echo $this->createUrl('domain/expire')?>"><?php echo Yii::t('dashboard','View all expiring domains')?></a>
      <?php endif?>
    </div>
  </div>
  <div class="row domain-events">
    <div class="span12">
      <h3><?php echo Yii::t('dashboard','Last events')?></h3>
      <table class="table table-striped">
        <thead>
          <tr>
            <th><?php echo Yii::t('dashboard','Domain')?></th>
            <?php if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN):?>
            <th><?php echo Yii::t('dashboard','Owner')?></th>
            <?php endif; ?>
            <th><?php echo Yii::t('dashboard','Appeared at')?></th>
            <th><?php echo Yii::t('dashboard','Type')?></th>
            <th><?php echo Yii::t('dashboard','Event')?></th>
          </tr>
        </thead>
        <tbody>
        <?php if ($lastEvents == array()):?>
          <tr>
            <td colspan="<?php if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN):?>5<?php else: ?>4<?php endif ?>">
              <span class="muted"><?php echo Yii::t('dashboard','No events found')?></span>
            </td>
          </tr>
        <?php endif?>
        <?php foreach ($lastEvents as $event):?>
          <tr>
            <td><?php echo CHtml::link($event->name,$this->createUrl('events/index',array(CHtml::activeName($event,'name')=>$event->name)))?></td>
            <?php if (Yii::app()->user->getModel()->role == User::ROLE_ADMIN):?>
            <td><?php echo $event->owner ? CHtml::link($event->owner->email,$this->createUrl('user/update',array('id'=>$event->idUser))) : $event->idUser?></td>
            <?php endif; ?>
            <td><?php echo Yii::app()->format->formatDatetime($event->create)?></td>
            <td><span class="label label-<?php echo $event->getAttributeTypeClass()?>"><?php echo $event->getAttributeTypeLabel()?></span></td>
            <td><?php echo Yii::t('events',$event->event,$event->getParam())?></td>
          </tr>
        <?php endforeach?>
        </tbody>
      </table>
      <a href="<?php echo $this->createUrl('events/index')?>"><?php echo Yii::t('dashboard','Browse events')?></a>
    </div>
  </div>
</div>

<script type="text/javascript">
  var chartStats = <?php echo CJavaScript::encode($stats)?>;

  var chartData = chartStats['day'];

  var chartOptions = {
    width: '99%',
    height: 300,
    backgroundColor: '#F0F0F0',
    chartArea: { left:50, top:15, width: '100%', height: '75%' },
    legend: { position: 'none' }
  };

  google.load('visualization', '1.0', {'packages':['corechart']});
  google.setOnLoadCallback(drawChart);
  function drawChart()
  {
    var chart = new google.visualization.LineChart(document.getElementById('q-hits'));
    chart.draw(google.visualization.arrayToDataTable(chartData), chartOptions);
  }

  window.onresize = drawChart;
</script>

<?php

$this->cs->registerScript('aTabsSelectorFunction',"
$('.period-selector a').click(function(e)
{
  e.preventDefault();
  $(this).parent().find('a').removeClass('active');
  $(this).addClass('active');
  chartData = chartStats[$(this).data('var')];
  drawChart();
});
");
