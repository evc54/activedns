<?php
/**
  Project       : ActiveDNS
  Document      : views/support/create.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support ticket's create form
*/
?>
<div class="container">
  <div class="span10 offset1">
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>get_class($model),
      'type'=>'horizontal',
    ))?>
      <fieldset>
        <legend><?php echo Yii::t('ticket','New ticket')?></legend>
        <?php echo $form->textFieldRow($model, 'subject', array('class'=>'span5'))?>
        <?php echo $form->textAreaRow($reply, 'text', array('rows'=>7,'class'=>'span5'))?>
      </fieldset>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><s class="icon-plus-sign icon-white"></s> <?php echo Yii::t('ticket','Add')?></button>
        <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    <?php $this->endWidget()?>
  </div>
</div>
