<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/search.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Resource records search
*/?>
<div class="container">
  <div class="row">
    <div class="span8 offset2">
      <h3><?php echo Yii::t('domain','Search <small>in resource records</small>')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span8 offset2">
      <form class="form-search" method="get" action="<?php echo $this->createUrl('search')?>">
        <div class="input-append">
          <input type="text" name="q" class="span4 search-query" value="<?php echo $model->query?>">
          <button type="submit" class="btn"><?php echo Yii::t('domain','Search')?></button>
        </div>
        <a href="<?php echo $this->createUrl('index')?>" class="btn btn-link"><?php echo Yii::t('common','Cancel')?></a>
      </form>
    </div>
  </div>
  <?php if (!empty($model->query)):?>
  <div class="row">
    <div class="span8 offset2">
      <h4><?php echo Yii::t('domain','Search results')?></h4>
    </div>
  </div>
  <div class="row">
    <div class="span8 offset2">
      <?php if (!empty($model->result)):?>
      <?php foreach ($model->result as $domain):?>
      <table class="table table-striped table-manage">
        <thead>
          <tr>
            <th colspan="3"><h4><a href="<?php echo $this->createUrl('update',array('id'=>$domain->id))?>"><?php echo $domain->getDomainName(false)?></a></h4></th>
          </tr>
          <tr>
            <th width="50"><?php echo Yii::t('domain','Type')?></th>
            <th width="200"><?php echo Yii::t('domain','Host')?></th>
            <th><?php echo Yii::t('domain','Target host')?></th>
            <th width="65"><?php echo Yii::t('domain','TTL')?></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($domain->currentZone->record as $record):?>
          <tr>
            <td><?php echo $record->type?></td>
            <td><?php echo $record->host?></td>
            <td><?php echo $record->rdata?></td>
            <td><?php echo $record->beautyTtl($record->ttl)?></td>
          </tr>
        <?php endforeach?>
        </tbody>
      </table>
      <?php endforeach?>
      <?php else:?>
      <span class="muted"><?php echo Yii::t('domain','No records found')?></span>
      <?php endif?>
    </div>
  </div>
  <?php endif?>
</div>
