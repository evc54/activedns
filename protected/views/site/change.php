<?php
/**
  Project       : ActiveDNS
  Document      : views/site/change.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Password change template
*/?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'type'=>'horizontal',
))?>
  <div class="modal fade restore" id="restore">
    <div class="modal-header">
      <h3><?php echo Yii::t('site','Change password')?></h3>
    </div>
    <div class="modal-body h500">
      <?php echo $form->textFieldRow($model, 'email', array('class'=>'input-large'))?>
      <?php echo $form->passwordFieldRow($model, 'newPassword', array('class'=>'input-large'))?>
      <?php echo $form->passwordFieldRow($model, 'newPasswordConfirm', array('class'=>'input-large'))?>
      <?php echo $form->captchaRow($model, 'captcha', array(
        'class'=>'input-large',
        'captchaOptions'=>array(
          'buttonLabel' => CHtml::image($this->themeUrl . '/i/refresh.gif',Yii::t('site','refresh')),
          'buttonOptions' => array('class'=>'refresh'),
        ),
      ))?>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary"><s class="icon-ok icon-white"></s> <?php echo Yii::t('common','Change')?></button>
    </div>
  </div>
<?php $this->endWidget()?>
<?php $this->cs->registerScript('ModalFlowOut',"$('#restore').modal('show');")?>
