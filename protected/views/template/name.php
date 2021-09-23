<?php
/**
  Project       : ActiveDNS
  Document      : views/template/name.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Template name update
*/?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'=>get_class($model),
  'type'=>'horizontal',
))?>
  <div class="container">
    <div class="row">
      <div class="span8 offset2">
        <fieldset>
          <legend>
            <?php if ($model->isNewRecord):?>
            <?php echo Yii::t('template','New template')?>
            <?php else:?>
            <?php echo Yii::t('template','Rename template ID {id}',array('{id}'=>$model->id))?>
            <?php endif?>
          </legend>
          <?php if (in_array(Yii::app()->user->getRole(),array(User::ROLE_ADMIN))):?>
          <?php echo $form->dropDownListRow($model,'type',$model->attributeLabelsType(),array('class'=>'span5'))?>
          <?php endif?>
          <?php echo $form->textFieldRow($model,'name',array('class'=>'span5'))?>
        </fieldset>
        <div class="form-actions">
          <?php if ($model->isNewRecord):?>
          <button type="submit" class="btn btn-primary"><?php echo Yii::t('template','Continue')?> <s class="icon-chevron-right icon-white"></s></button>
          <?php else:?>
          <button type="submit" class="btn btn-primary"><s class="icon-save icon-white"></s> <?php echo Yii::t('common','Save')?></button>
          <?php endif?>
          <a href="<?php echo $this->createUrl('index')?>" class="btn btn-link"><?php echo Yii::t('common','Cancel')?></a>
        </div>
      </div>
    </div>
  </div>
<?php $this->endWidget()?>
