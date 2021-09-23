<?php
/**
  Project       : ActiveDNS
  Document      : views/account/index.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account options
*/
?>
<div class="container">
  <form class="form-horizontal">
  <div class="row">
    <div class="span12">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <fieldset>
        <legend><?php echo Yii::t('account','Account options')?></legend>
      </fieldset>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Current pricing plan')?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" class="span2">
            <a href="#"
              class="popover-link"
              rel="popover"
              title="<?php echo $model->plan->title?>"
              data-placement="bottom"
              data-trigger="hover"
              data-html="true"
              data-content="<?php echo CHtml::encode('<div class="features">' . $this->renderPartial('//snippets/plan',array('plan'=>$model->plan,'cycles'=>$model->plan->attributeLabelsBilling(),),true) . '</div>')?>">
              <?php echo $model->plan->title?>
            </a>
          </span>
          <a href="<?php echo $this->createUrl('upgrade')?>" class="btn btn-primary btn-small" style="width: 60px; position: relative; top: 3px;"><?php echo Yii::t('account','Upgrade')?></a>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Billing cycle')?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" class="span2"><?php echo Yii::t('account','Annually')?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Account paid till')?></label>
        <div class="controls">
          <?php if (!$model->paidTill):?>
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" class="span2">&mdash;</span>
          <?php else:?>
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" class="span2"><a href="#" class="popover-link" rel="tooltip" title="<?php echo $this->expireAfter($model->paidTill)?>"><?php echo Yii::app()->format->formatDate($model->paidTill)?></a></span>
          <a href="<?php echo $this->createUrl('renew')?>" class="btn btn-primary btn-small" style="width: 60px; position: relative; top: 3px;"><?php echo Yii::t('account','Renew')?></a>
          <?php endif?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <fieldset>
        <legend><?php echo Yii::t('account','Danger actions')?></legend>
      </fieldset>
      <div>
        <a href="<?php echo $this->createUrl('remove')?>" class="btn btn-danger"><?php echo Yii::t('account','Delete account')?></a>
      </div>
    </div>
  </div>
  </form>
</div>
