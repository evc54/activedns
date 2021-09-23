<?php
/**
  Project       : ActiveDNS
  Document      : views/plan/disable.php
  Document type : PHP script file
  Created at    : 05.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plan's disable confirmation
*/?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('pricingPlan','Are you sure to disable pricing plan?')?></h2>
    </div>
  </div>
  <div class="row">
    <div class="span10 offset1">
      <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>get_class($model),
        'type'=>'horizontal',
      ))?>
        <?php $this->renderPartial('detail',array(
          'model'=>$model,
        ))?>
        <div class="form-actions">
          <button type="submit" class="btn btn-inverse"><s class="icon-ban-circle icon-white"></s> <?php echo Yii::t('common','Disable')?></button>
          <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
