<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/plan.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plan template
*/?>
<div class="price-an">
<?php if ($plan->pricePerYear >= 0):?>
<?php /*
<sub>$</sub><?php echo floor($plan->pricePerYear)?><sup>.<?php echo floor($plan->pricePerYear * 100 % 100)?></sup> <span><?php echo Yii::t('pricingPlan','per year')?></span>
 */ ?>
<?php echo CurrencyHelper::render($plan->pricePerYear,array(
  'signTag'=>'sub',
  'decimalsTag'=>'sup',
))?> <span><?php echo Yii::t('pricingPlan','per year')?></span>
<?php else:?>
&nbsp;
<?php endif?>
</div>
<div class="price-mo">
<?php if ($plan->pricePerMonth >= 0):?>
<?php /*
<sub>$</sub><?php echo floor($plan->pricePerMonth)?><sup>.<?php echo floor($plan->pricePerMonth * 100 % 100)?></sup> <span><?php echo Yii::t('pricingPlan','per month')?></span>
 */ ?>
<?php echo CurrencyHelper::render($plan->pricePerMonth,array(
  'signTag'=>'sub',
  'decimalsTag'=>'sup',
))?> <span><?php echo Yii::t('pricingPlan','per month')?></span>
<?php else:?>
&nbsp;
<?php endif?>
</div>
<div class="plan-feature"><?php echo Yii::t('pricingPlan','Domains')?>
<?php if ($plan->domainsQty > 0):?>
  <span><?php echo $plan->domainsQty?></span>
<?php else:?>
  <span class="infinite">&infin;</span>
<?php endif?>
</div>
<div class="plan-feature"><?php echo Yii::t('pricingPlan','Users')?>
<?php if ($plan->usersQty > 0):?>
  <span><?php echo $plan->usersQty?></span>
<?php else:?>
  <span class="infinite">&infin;</span>
<?php endif?>
</div>
<div class="plan-feature"><?php echo Yii::t('pricingPlan','Nameservers')?>
  <span><?php echo $plan->nameserversQty?></span>
</div>
<div class="plan-feature"><?php echo Yii::t('pricingPlan','Minimum TTL')?>
  <span><?php echo ResourceRecord::model()->beautyTtl($plan->minTtl)?></span>
</div>
<?php if ($plan->accessApi):?>
<div class="plan-feature"><?php echo Yii::t('pricingPlan','Management API')?><span><?php echo Yii::t('common','yes')?></span></div>
<?php endif?>
<div class="plan-feature"><?php echo Yii::t('pricingPlan','# of requests')?><span class="infinite">&infin;</span></div>
<div class="plan-feature"><?php echo Yii::t('pricingPlan','Billing cycle')?><span class="text-right"><?php echo strtolower(strtr($cycles[$plan->billing],array(' '=>'<br />')))?></span></div>
