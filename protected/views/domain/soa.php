<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/soa.php
  Document type : PHP script file
  Created at    : 01.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Start of authority info block
*/?>
<div id="soa">
  <h4><?php echo Yii::t('domain','Start of authority')?></h4>
  <div class="well active-menu">
    <ul class="nav nav-list">
      <li class="nav-header"><?php echo Yii::t('domain','Hostmaster')?></li>
      <li><?php echo $zone->hostmaster?></li>
      <li class="nav-header"><?php echo Yii::t('domain','Refresh')?></li>
      <li><?php echo ResourceRecord::model()->beautyTtl($zone->refresh)?></li>
      <li class="nav-header"><?php echo Yii::t('domain','Retry')?></li>
      <li><?php echo ResourceRecord::model()->beautyTtl($zone->retry)?></li>
      <li class="nav-header"><?php echo Yii::t('domain','Expiry')?></li>
      <li><?php echo ResourceRecord::model()->beautyTtl($zone->expire)?></li>
      <li class="nav-header"><?php echo Yii::t('domain','Minimum TTL')?></li>
      <li><?php echo ResourceRecord::model()->beautyTtl($zone->minimum)?></li>
      <li><a class="soa-update" href="<?php echo $this->createUrl('ajax',array('id'=>$zone->id,'ajax'=>'updateSOA'))?>"><s class="icon-pencil icon-black"></s> <?php echo Yii::t('domain','Edit start of authority')?></a></li>
    </ul>
  </div>
</div>
