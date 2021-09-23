<?php
/**
  Project       : ActiveDNS
  Document      : views/template/view.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : View template page
*/?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('template','{name} <small>template view</small>',array('{name}'=>$model->name))?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <?php if ($model->type == Template::TYPE_PRIVATE):?>
            <li><a href="<?php echo $this->createUrl('index')?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('template','Templates management')?></a></li>
          <?php else:?>
            <li><a href="<?php echo $this->createUrl('common')?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('template','View common templates')?></a></li>
          <?php endif?>
        </ul>
        <?php if ($model->type == Template::TYPE_PRIVATE || in_array(Yii::app()->user->getRole(),array(User::ROLE_ADMIN))):?>
          <ul class="nav nav-list">
            <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
            <li><a href="<?php echo $this->createUrl('update',array('id'=>$model->id))?>"><s class="icon-pencil icon-black"></s> <?php echo Yii::t('template','Edit template')?></a></li>
          </ul>
        <?php endif?>
      </div>
    </div>
    <div class="span9">
      <div class="active-editor" id="active-editor">
        <?php if ($model->hasRecords(ResourceRecord::TYPE_A)):?>
          <h4><?php echo ResourceRecord::TYPE_A?> <small class="muted"><?php echo ContextHelp::ResourceRecord(ResourceRecord::TYPE_A)?></small></h4>
          <table class="table table-striped domain-manager">
            <thead>
              <tr>
                <th width="200"><?php echo Yii::t('domain','Host')?></th>
                <th><?php echo Yii::t('domain','Points to')?></th>
                <th width="60"><?php echo Yii::t('domain','TTL')?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->getRecords(ResourceRecord::TYPE_A) as $record):?>
                <tr>
                  <td><?php echo $record->host?></td>
                  <td><?php echo $record->rdata?></td>
                  <td><?php echo Yii::app()->user->beautyTtl($record->ttl)?></td>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
        <?php if ($model->hasRecords(ResourceRecord::TYPE_AAAA)):?>
          <h4><?php echo ResourceRecord::TYPE_AAAA?> <small class="muted"><?php echo ContextHelp::ResourceRecord(ResourceRecord::TYPE_AAAA)?></small></h4>
          <table class="table table-striped domain-manager">
            <thead>
              <tr>
                <th width="200"><?php echo Yii::t('domain','Host')?></th>
                <th><?php echo Yii::t('domain','Points to')?></th>
                <th width="60"><?php echo Yii::t('domain','TTL')?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->getRecords(ResourceRecord::TYPE_AAAA) as $record):?>
                <tr>
                  <td><?php echo $record->host?></td>
                  <td><?php echo $record->rdata?></td>
                  <td><?php echo Yii::app()->user->beautyTtl($record->ttl)?></td>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
        <?php if ($model->hasRecords(ResourceRecord::TYPE_CNAME)):?>
          <h4><?php echo ResourceRecord::TYPE_CNAME?> <small class="muted"><?php echo ContextHelp::ResourceRecord(ResourceRecord::TYPE_CNAME)?></small></h4>
          <table class="table table-striped domain-manager">
            <thead>
              <tr>
                <th width="200"><?php echo Yii::t('domain','Host')?></th>
                <th><?php echo Yii::t('domain','Points to')?></th>
                <th width="60"><?php echo Yii::t('domain','TTL')?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->getRecords(ResourceRecord::TYPE_CNAME) as $record):?>
                <tr>
                  <td><?php echo $record->host?></td>
                  <td><?php echo $record->rdata?></td>
                  <td><?php echo Yii::app()->user->beautyTtl($record->ttl)?></td>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
        <?php if ($model->hasRecords(ResourceRecord::TYPE_MX)):?>
          <h4><?php echo ResourceRecord::TYPE_MX?> <small class="muted"><?php echo ContextHelp::ResourceRecord(ResourceRecord::TYPE_MX)?></small></h4>
          <table class="table table-striped domain-manager">
            <thead>
              <tr>
                <th width="200"><?php echo Yii::t('domain','Host')?></th>
                <th><?php echo Yii::t('domain','Points to')?></th>
                <th width="100"><?php echo Yii::t('domain','Priority')?></th>
                <th width="60"><?php echo Yii::t('domain','TTL')?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->getRecords(ResourceRecord::TYPE_MX) as $record):?>
                <tr>
                  <td><?php echo $record->host?></td>
                  <td><?php echo $record->rdata?></td>
                  <td><?php echo $record->priority?></td>
                  <td><?php echo Yii::app()->user->beautyTtl($record->ttl)?></td>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
        <?php if ($model->hasRecords(ResourceRecord::TYPE_SRV)):?>
          <h4><?php echo ResourceRecord::TYPE_SRV?> <small class="muted"><?php echo ContextHelp::ResourceRecord(ResourceRecord::TYPE_SRV)?></small></h4>
          <table class="table table-striped domain-manager">
            <thead>
              <tr>
                <th width="150"><?php echo Yii::t('domain','Service')?></th>
                <th width="50"><?php echo Yii::t('domain','Proto')?></th>
                <th width="50"><?php echo Yii::t('domain','Priority')?></th>
                <th width="50"><?php echo Yii::t('domain','Weight')?></th>
                <th width="50"><?php echo Yii::t('domain','Port')?></th>
                <th><?php echo Yii::t('domain','Target')?></th>
                <th width="60"><?php echo Yii::t('domain','TTL')?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->getRecords(ResourceRecord::TYPE_SRV) as $record):?>
                <tr>
                  <td><?php echo $record->host?></td>
                  <td><?php echo $record->proto?></td>
                  <td><?php echo $record->priority?></td>
                  <td><?php echo $record->weight?></td>
                  <td><?php echo $record->port?></td>
                  <td><?php echo $record->rdata?></td>
                  <td><?php echo Yii::app()->user->beautyTtl($record->ttl)?></td>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
        <?php if ($model->hasRecords(ResourceRecord::TYPE_NS)):?>
          <h4><?php echo ResourceRecord::TYPE_NS?> <small class="muted"><?php echo ContextHelp::ResourceRecord(ResourceRecord::TYPE_NS)?></small></h4>
          <table class="table table-striped domain-manager">
            <thead>
              <tr>
                <th width="200"><?php echo Yii::t('domain','Host')?></th>
                <th><?php echo Yii::t('domain','Points to')?></th>
                <th width="60"><?php echo Yii::t('domain','TTL')?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->getRecords(ResourceRecord::TYPE_NS) as $record):?>
                <tr>
                  <td><?php echo $record->host?></td>
                  <td><?php echo $record->rdata?></td>
                  <td><?php echo Yii::app()->user->beautyTtl($record->ttl)?></td>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
        <?php if ($model->hasRecords(ResourceRecord::TYPE_TXT)):?>
          <h4><?php echo ResourceRecord::TYPE_TXT?> <small class="muted"><?php echo ContextHelp::ResourceRecord(ResourceRecord::TYPE_TXT)?></small></h4>
          <table class="table table-striped table-manage">
            <thead>
              <tr>
                <th width="200"><?php echo Yii::t('domain','Host')?></th>
                <th><?php echo Yii::t('domain','Points to')?></th>
                <th width="60"><?php echo Yii::t('domain','TTL')?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($model->getRecords(ResourceRecord::TYPE_TXT) as $record):?>
                <tr>
                  <td><?php echo $record->host?></td>
                  <td><?php echo $record->rdata?></td>
                  <td><?php echo Yii::app()->user->beautyTtl($record->ttl)?></td>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
      </div>
    </div>
  </div>
</div>
