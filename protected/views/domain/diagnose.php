<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/diagnose.php
  Document type : PHP script file
  Created at    : 30.09.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain diagnose page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('domain','Domain alerts diagnose')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <?php $this->renderPartial('grids/diagnose',array(
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
