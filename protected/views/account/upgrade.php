<?php
/**
  Project       : ActiveDNS
  Document      : views/account/upgrade.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account upgrade template
*/?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'   => get_class($model),
    'type' => 'horizontal',
))?>
<div class="container">
  <div class="row">
    <div class="span8 offset2">
      <fieldset>
        <legend><?php echo Yii::t('account','Account upgrade options')?></legend>
      </fieldset>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Select account plan')?></label>
        <div class="controls">
          <ul class="unstyled" id="select-plan">
          <?php foreach ($plans as $plan):?>
          <?php $disabled = $plan->domainsQty > 0 && $model->totalDomainsQty > $plan->domainsQty?>
          <?php $selected = $plan->id == $model->plan->id?>
            <li>
              <label class="radio" style="display: block;">
                <input type="radio"<?php if ($disabled):?> disabled="disabled"<?php endif?><?php if ($selected):?>checked="checked"<?php endif?> name="plan" value="<?php echo $plan->id?>">
                <a href="#"
                  class="popover-link<?php if ($disabled):?> disabled-item<?php endif?>"
                  rel="popover"
                  title="<?php echo $plan->title?>"
                  data-placement="bottom"
                  data-trigger="hover"
                  data-html="true"
                  data-content="<?php echo CHtml::encode('<div class="features">' . $this->renderPartial('//snippets/plan',array('plan'=>$plan,'cycles'=>$plan->attributeLabelsBilling(),),true) . '</div>')?>">
                  <?php echo $plan->title?>
                </a>
              </label>
            </li>
          <?php endforeach?>
          </ul>
          <span class="help-block"><?php echo Yii::t('account','Some plans may be not suitable for your number of domains')?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="billing"><?php echo Yii::t('account','Select billing cycle')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('billing',$model->billing,$model->plan->getBillingOptions())?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account','Next billing date')?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" id="next-billing-date"><?php echo $model->paidTill ? Yii::app()->format->formatDate($model->paidTill) : '&mdash;'; ?></span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('account',"You'll be charged")?></label>
        <div class="controls">
          <span style="font-size: 15px; display: inline-block; margin: 6px 0;" id="charge-amount"><?php echo CurrencyHelper::render(0)?></span>
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
<?php $this->cs->registerScript(get_class($model) . '_PricingAjax',"
$('#select-plan a').click(function(e)
{
  e.preventDefault();
  $(this).parent().find('input').click();
});
$('#select-plan input').click(function(e)
{
  var plan = $(this);
  $.ajax({ data: { plan: plan.val() }, type: 'get', dataType: 'json', success: function(jdata)
  {
    plan.prop('checked',true);
    $('#billing').html(jdata.billing);
    $('#charge-amount').html(jdata.charge);
    $('#next-billing-date').text(jdata.date);
  }});
});
$('#billing').change(function(e)
{
  var plan = $('#select-plan input:checked');
  var billing = $(this).val();
  $.ajax({ data: { plan: plan.val(), billing: billing }, type: 'get', dataType: 'json', success: function(jdata)
  {
    $('#charge-amount').html(jdata.charge);
    $('#next-billing-date').text(jdata.date);
  }});
});")?>
