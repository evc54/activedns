<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/expire.php
  Document type : PHP script file
  Created at    : 13.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Expiring domains list page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('domain',"Domain's expiring soon")?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <?php $this->renderPartial('grids/expire',array(
        'model'=>$model,
      ))?>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <a href="<?php echo $this->createUrl('index')?>" class="btn"><s class="icon-chevron-left icon-black"></s> <?php echo Yii::t('common','Return back')?></a>
    </div>
  </div>
</div>
