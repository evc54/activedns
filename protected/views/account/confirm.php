<?php
/**
  Project       : ActiveDNS
  Document      : views/account/confirm.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account upgrade confirmation
*/?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'   => get_class($model),
    'type' => 'horizontal',
))?>
<div class="container">
  <div class="row">
    <div class="span8 offset2">
      <fieldset>
        <legend><?php echo isset($type) && $type == 'renew' ? Yii::t('account','Renew account confirmation') : Yii::t('account','Account upgrade confirmation')?></legend>
      </fieldset>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Chosen account plan')?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" class="span2">
            <a href="#"
              class="popover-link"
              rel="popover"
              title="<?php echo $plan->title?>"
              data-placement="bottom"
              data-trigger="hover"
              data-html="true"
              data-content="<?php echo CHtml::encode('<div class="features">' . $this->renderPartial('//snippets/plan',array('plan'=>$plan,'cycles'=>$plan->attributeLabelsBilling(),),true) . '</div>')?>">
              <?php echo $plan->title?>
            </a>
          </span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Chosen billing cycle')?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" class="span2">
            <?php echo $plan->getAttributeLabelBilling($cycle)?>
          </span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account',"You'll be charged")?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" id="charge-amount"><?php echo $params['charge']?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Next billing date')?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" id="next-billing-date"><?php echo $params['date']?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label">&nbsp;</label>
        <div class="controls">
          <span class="help-block">
            <?php echo Yii::t('account','Note: you will be redirected to payment gateway')?>
          </span>
        </div>
      </div>
      <div class="form-actions">
        <a href="<?php echo $this->createUrl('checkout')?>" class="btn btn-primary"><?php echo Yii::t('common','Continue')?> <s class="icon-chevron-right icon-white"></s></a>
        <a href="<?php echo $this->createUrl('index')?>" class="btn btn-link"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    </div>
  </div>
</div>
<?php $this->endWidget()?>
