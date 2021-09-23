<?php
/**
  Project       : ActiveDNS
  Document      : views/site/signin.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Sign in template
*/
?>
<?php $this->renderPartial('index')?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'action'=>$this->createUrl('site/signin'),
  'type'=>'horizontal',
  'htmlOptions'=>array(
    'class'=>'modal signin fade',
    'id'=>'bigSignInForm',
  ),
))?>
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo Yii::t('site','Sign in')?></h3>
  </div>
  <div class="modal-body">
    <?php echo $form->textFieldRow($model, 'username', array('class'=>'input-xlarge'))?>
    <?php echo $form->passwordFieldRow($model, 'password', array('class'=>'input-xlarge'))?>
    <?php echo $form->checkBoxRow($model,'stayLoggedIn')?>
    <p class="modal-help-block"><span class="required">*</span> <?php echo Yii::t('site','required fields')?></p>
  </div>
  <div class="modal-footer">
    <a class="btn btn-link" href="<?php echo $this->createUrl('site/restore',array('username'=>$model->username))?>"><?php echo Yii::t('site','Restore password')?></a>
    <button class="btn btn-primary" type="submit"><s class="icon-ok icon-white"></s> <?php echo Yii::t('site','Sign in')?></button>
  </div>
<?php $this->endWidget()?>
<?php $this->cs->registerScript('activateSignInModal',"
$('.modal.signin').modal('show').on('shown',function(){ $('#" . CHtml::activeId($model,'password') . "','#bigSignInForm').focus(); });
")?>
