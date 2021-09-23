<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/enable.php
  Document type : PHP script file
  Created at    : 06.04.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain's enable confirmation
*/?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('domain','Are you sure to enable domain {domain}?',array('{domain}'=>$model->name))?></h2>
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
          <button type="submit" class="btn btn-success"><s class="icon-ok-sign icon-white"></s> <?php echo Yii::t('domain','Enable')?></button>
          <a class="btn btn-link" href="<?php echo $returnUrl?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
        <?php echo CHtml::hiddenField('returnUrl',$returnUrl)?>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
