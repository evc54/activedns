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
      <h3><?php echo Yii::t('account','Your account has been upgraded successfully')?></h3>
      <p>
        <h4><?php echo Yii::t('account','Thank you!')?></h4>
        <?php echo Yii::t('account','Please wait a few moments while gateway confirms your payment.')?>
      </p>
      <a href="<?php echo $this->createUrl('index')?>" class="btn btn-success"><?php echo Yii::t('common','Continue')?> <s class="icon-chevron-right icon-white"></s></a>
    </div>
  </div>
</div>
