<?php
/**
  Project       : ActiveDNS
  Document      : views/site/restore.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Password restore template
*/
?>
<?php $this->renderPartial('index')?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type'=>'horizontal',
  'htmlOptions'=>array(
    'class'=>'modal restore fade',
  ),
))?>
  <div class="modal-header">
    <h3><?php echo Yii::t('site','Restore password')?></h3>
  </div>
  <div class="modal-body">
    <?php echo $form->textFieldRow($model, 'email', array('class'=>'input-xlarge'))?>
    <?php echo $form->captchaRow($model, 'captcha', array(
      'class'=>'input-large',
      'captchaOptions'=>array(
        'buttonLabel' => CHtml::image($this->themeUrl . '/i/refresh.gif',Yii::t('site','refresh')),
        'buttonOptions' => array('class'=>'refresh')
      ),
    ))?>
    <p class="modal-help-block"><span class="required">*</span> <?php echo Yii::t('site','required fields')?></p>
  </div>
  <div class="modal-footer">
    <a class="btn btn-link" href="#" data-dismiss="modal"><?php echo Yii::t('common','Cancel')?></a>
    <button class="btn btn-primary" type="submit"><s class="icon-ok icon-white"></s> <?php echo Yii::t('site','Restore')?></button>
  </div>
<?php $this->endWidget()?>
<?php $this->cs->registerScript('activateRestoreModal',"
$('.modal.restore').modal('show');
")?>
