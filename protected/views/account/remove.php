<?php
/**
  Project       : ActiveDNS
  Document      : views/account/remove.php
  Document type : PHP script file
  Created at    : 11.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account removal confirmation
*/?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'     => get_class($model),
  'type'   => 'horizontal',
  'method' => 'post',
))?>
<div class="container">
  <div class="row">
    <div class="span8 offset2">
      <fieldset>
        <legend><?php echo Yii::t('account','Account removal')?></legend>
        <?php if ($model->totalDomainsQty > 0):?>
        <div class="control-group">
          <label class="control-label"><?php echo Yii::t('account','Note')?></label>
          <div class="controls">
            <span style="font-size: 15px; display: inline-block; margin: 6px 0;" class="span6">
              <?php echo Yii::t('account','You have {n} domain hosted. To remove your account please delete it first.|You have {n} domains hosted. To remove your account please delete it first.',array($model->totalDomainsQty))?>
            </span>
          </div>
        </div>
        <?php else:?>
        <div class="alert alert-warning">
          <strong><?php echo Yii::t('account','Warning!')?></strong>
          <?php echo Yii::t('account','Account deletion is non-reversable action!')?>
        </div>
        <?php echo $form->textFieldRow($removal,'confirmation',array('class'=>'span4','hint'=>$removal->getError('confirmation') ? null : Yii::t('account','Please type in the text field above: {phrase}',array('{phrase}'=>CHtml::tag('strong',array(),Yii::t('account','I confirm my account removal'))))))?>
        <?php endif?>
      </fieldset>
      <div class="form-actions">
        <button class="btn btn-danger"<?php if ($model->totalDomainsQty > 0):?> disabled="disabled"<?php endif?>><?php echo Yii::t('account','Delete account')?></button>
        <a href="<?php echo $this->createUrl('index')?>" class="btn btn-link"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    </div>
  </div>
</div>
<?php $this->endWidget()?>
