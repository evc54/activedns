<?php
/**
  Project       : ActiveDNS
  Document      : views/account/success.php
  Document type : PHP script file
  Created at    : 06.04.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account upgrade success template
*/?>
<div class="container">
  <div class="row">
    <div class="span8 offset2">
      <h3><?php echo Yii::t('account','An error occurred while processing your payment')?></h3>
      <h4><?php echo Yii::t('account','We are sorry, but your payment was unsuccessful.')?></h4>
      <p><?php echo Yii::t('account','If you feel that is mistake, please')?> <a href="<?php echo $this->createUrl('/support/index')?>"><?php echo Yii::t('account','contact support')?></a></p>
      <a href="<?php echo $this->createUrl('index')?>" class="btn btn-danger"><?php echo Yii::t('common','Continue')?> <s class="icon-chevron-right icon-white"></s></a>
    </div>
  </div>
</div>
