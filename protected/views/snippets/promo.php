<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/promo.php
  Document type : PHP script file
  Created at    : 11.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Promotional block
*/?>
<div class="row row-feature">
  <div class="span1 offset1"><img src="<?php echo $this->imageUrl('ico-easy.png')?>" /></div>
  <div class="span4 feature">
    <div><?php echo Yii::t('site','Easiest Domain Management')?></div>
    <span><?php echo Yii::t('site','Manage your domains with a few mouse clicks')?></span>
  </div>
  <div class="span1"><img src="<?php echo $this->imageUrl('ico-ns.png')?>" /></div>
  <div class="span4 feature">
    <div><?php echo Yii::t('site','Up To Four Nameservers')?></div>
    <span><?php echo Yii::t('site','Provides up to 4 nameservers for each domain')?></span>
  </div>
</div>
<div class="row row-feature">
  <div class="span1 offset1"><img src="<?php echo $this->imageUrl('ico-ttl.png')?>" /></div>
  <div class="span4 feature">
    <div><?php echo Yii::t('site','As Low TTL As 1 Second')?></div>
    <span><?php echo Yii::t('site','All premium account users can set TTL for each record to 1 second')?></span>
  </div>
  <div class="span1"><img src="<?php echo $this->imageUrl('ico-tpl.png')?>" /></div>
  <div class="span4 feature">
    <div><?php echo Yii::t('site','Administrative Templates')?></div>
    <span><?php echo Yii::t('site','Allows to create any type of domain management templates')?></span>
  </div>
</div>
<div class="row row-feature">
  <div class="span1 offset1"><img src="<?php echo $this->imageUrl('ico-ddos.png')?>" /></div>
  <div class="span4 feature">
    <div><?php echo Yii::t('site','Active DDOS protection')?></div>
    <span><?php echo Yii::t('site','Set up request limit for a domain and this feature would activate')?></span>
  </div>
  <div class="span1"><img src="<?php echo $this->imageUrl('ico-dyn.png')?>" /></div>
  <div class="span4 feature">
    <div><?php echo Yii::t('site','Dynamic DNS API')?></div>
    <span><?php echo Yii::t('site',"Change your domain's zone records at any time in a dynamic way")?></span>
  </div>
</div>
<div class="row sign">
  <a href="<?php echo $this->createUrl('site/pricing')?>"><?php echo Yii::t('site','See pricing plans')?></a> <?php echo Yii::t('site','or')?> <a href="#signup-modal" data-toggle="modal"><?php echo Yii::t('site','Sign up for free')?></a>
</div>
