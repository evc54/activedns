<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/info.php
  Document type : PHP script file
  Created at    : 10.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain info block
*/?>
<div id="info">
  <h4><?php echo Yii::t('domain','Domain info')?></h4>
  <div class="well active-menu">
    <ul class="next nav nav-list">
      <li class="nav-header"><?php echo Yii::t('common','status')?></li>
      <li><?php $this->renderPartial('labels/status',array(
        'model'=>$model,
        'noTip'=>true,
      ))?></li>
      <li class="nav-header"><?php echo Yii::t('domain','Info autochecking allowed')?></li>
      <li><?php echo Yii::app()->format->formatBoolean($model->allowAutoCheck)?></li>
      <li class="nav-header"><?php echo Yii::t('domain','domain created at')?></li>
      <li><?php echo $model->register ? Yii::app()->format->formatDate($model->register) : '&mdash;'; ?></li>
      <li class="nav-header"><?php echo Yii::t('domain','domain expires at')?></li>
      <li><?php echo $model->expire ? Yii::app()->format->formatDate($model->expire) : '&mdash;'; ?></li>
      <li class="nav-header"><?php echo Yii::t('domain','assigned nameservers')?></li>
      <?php if ($nameservers = $model->getDomainNameservers($zone)):?>
      <?php foreach ($nameservers as $ns):?>
      <li><a href="javascript:void(0)"><?php echo $ns?></a></li>
      <?php endforeach?>
      <?php else:?>
      <li><a href="javascript:void(0)" class="disabled-item"><?php echo Yii::t('domain','None assigned')?></a></li>
      <?php endif?>
      <li class="nav-header"><?php echo Yii::t('domain','current nameservers')?></li>
      <?php if ($model->ns1 || $model->ns2 || $model->ns3 || $model->ns4):?>
      <?php foreach (array('ns1','ns2','ns3','ns4') as $attribute):?>
      <?php if ($ns = $model->getAttribute($attribute)):?>
      <li><a href="javascript:void(0)"><?php echo $ns?></a></li>
      <?php endif?>
      <?php endforeach?>
      <?php else:?>
      <li><a href="javascript:void(0)" class="disabled-item"><?php echo Yii::t('domain','None set')?></a></li>
      <?php endif?>
      <li class="nav-header"><?php echo Yii::t('domain','registrar')?></li>
      <li><?php echo $model->registrar?></li>
      <li><a class="info-update" href="<?php echo $this->createUrl('ajax',array('id'=>$model->id,'ajax'=>'updateInfo'))?>"><s class="icon-pencil icon-black"></s> <?php echo Yii::t('domain','Edit domain info')?></a></li>
    </ul>
  </div>
</div>
