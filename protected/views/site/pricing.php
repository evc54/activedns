<?php
/**
  Project       : ActiveDNS
  Document      : site/pricing.php
  Document type : PHP script file
  Created at    : 04.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plans page
*/
?>
<div class="container">
  <div class="row">
    <?php $this->renderPartial('//snippets/logo')?>
  </div>
  <?php foreach ($types as $type => $title):?>
    <div class="row features">
      <div class="span12">
        <h2><?php echo $title?> <?php echo Yii::t('pricingPlan','plans')?></h2>
      </div>
    </div>
    <div class="row features">
      <?php foreach ($data[$type] as $c => $plan):?>
        <div class="span<?php echo count($data[$type]) > 1 ? 5 : 10?><?php if (($c+1) % 2 != 0):?> offset1<?php endif?>">
          <div class="plan-wr">
            <div class="plan">
              <h3><?php echo $plan->title?></h3>
              <?php $this->renderPartial('//snippets/plan',array(
                'plan'=>$plan,
                'cycles'=>$cycles,
              ))?>
              <div class="signup-link">
                <a href="#signup-modal" data-toggle="modal" class="btn btn-success btn-small"><s class="icon-thumbs-up icon-white"></s> <?php echo Yii::t('site','Sign up')?></a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach?>
    </div>
  <?php endforeach?>
  <div class="row sign">
    <div class="span12">
      <h5><?php echo Yii::t('site','Get an account for free and then upgrade it in control panel to chosen plan')?></h5>
    </div>
  </div>
</div>
