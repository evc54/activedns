<?php
/**
  Project       : ActiveDNS
  Document      : vies/user/enable.php
  Document type : PHP script file
  Created at    : 16.09.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : User's enable confirmation
*/
?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('user','Are you sure to enable user account?')?></h2>
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
          <button type="submit" class="btn btn-success"><s class="icon-ok-circle icon-white"></s> <?php echo Yii::t('common','Enable')?></button>
          <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
