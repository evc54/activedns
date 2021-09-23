<?php
/**
  Project       : ActiveDNS
  Document      : views/account/renew.php
  Document type : PHP script file
  Created at    : 11.04.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account renewal template
*/?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'   => get_class($model),
  'type' => 'horizontal',
))?>
<div class="container">
  <div class="row">
    <div class="span8 offset2">
      <fieldset>
        <legend><?php echo Yii::t('account','Renew your account')?></legend>
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
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" id="next-billing-date"><?php echo $model->paidTill ? Yii::app()->format->formatDate($model->paidTill) : '&mdash;'; ?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account',"You'll be charged")?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" id="charge-amount"><?php echo CurrencyHelper::render($charge)?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Next billing date')?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" id="next-billing-date"><?php echo $renewDate ? Yii::app()->format->formatDate($renewDate) : '&mdash;'; ?></span>
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary"><?php echo Yii::t('common','Continue')?> <s class="icon-chevron-right icon-white"></s></button>
        <a href="<?php echo $this->createUrl('index')?>" class="btn btn-link"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    </div>
  </div>
</div>
<?php $this->endWidget()?>
<?php $this->cs->registerCss(get_class($model) . '_Popover',"
.popover {
  min-width: 280px;
  padding-bottom: 10px;
}
")?>
