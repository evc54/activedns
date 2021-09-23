<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/delete.php
  Document type : PHP script file
  Created at    : 06.04.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain's delete confirmation
*/?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('domain','Are you sure to remove domain {domain}?',array('{domain}'=>$model->name))?></h2>
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
          <button type="submit" class="btn btn-danger"><s class="icon-trash icon-white"></s> <?php echo Yii::t('common','Delete')?></button>
          <a class="btn btn-link" href="<?php echo $returnUrl?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
        <?php echo CHtml::hiddenField('returnUrl',$returnUrl)?>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
