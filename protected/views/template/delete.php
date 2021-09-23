<?php
/**
  Project       : ActiveDNS
  Document      : views/template/delete.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Templates delete confirmation
*/
?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('template','Are you sure to delete template?')?></h2>
    </div>
  </div>
  <div class="row">
    <div class="span10 offset1">
      <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>get_class($model),
        'type'=>'horizontal',
      ))?>
        <?php $this->widget('bootstrap.widgets.TbDetailView', array(
          'data'=>$model,
          'attributes'=>array(
            array('name'=>'id'),
            array('name'=>'name'),
            array('name'=>'recordsQty'),
          ),
        ))?>
        <div class="form-actions">
          <button type="submit" class="btn btn-danger"><s class="icon-trash icon-white"></s> <?php echo Yii::t('common','Delete')?></button>
          <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
