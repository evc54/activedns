<?php
/**
  Project       : ActiveDNS
  Document      : views/plan/update.php
  Document type : PHP script file
  Created at    : 05.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plan's update page
*/?>
<div class="container">
  <div class="span10 offset1">
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>get_class($model),
      'type'=>'horizontal',
    ))?>
      <fieldset>
        <legend><?php echo $model->isNewRecord ? Yii::t('pricingPlan','New pricing plan') : Yii::t('pricingPlan','Update pricing plan ID {id}',array('{id}'=>$model->id))?></legend>
        <?php echo $form->dropDownListRow($model, 'status', $model->attributeLabelsStatus(), array('class'=>'span5'))?>
        <?php echo $form->dropDownListRow($model, 'type', $model->attributeLabelsType(), array('class'=>'span5'))?>
        <?php echo $form->textFieldRow($model, 'title', array('class'=>'span5'))?>
        <?php echo $form->textFieldRow($model, 'domainsQty', array('class'=>'span5'))?>
        <?php echo $form->textFieldRow($model, 'usersQty', array('class'=>'span5'))?>
        <?php echo $form->dropDownListRow($model, 'nameserversQty', array(2=>2,4=>4), array('class'=>'span5'))?>
        <?php echo $form->dropDownListRow($model, 'defaultNameserverMaster', $masterNameservers, array('empty'=>'','class'=>'span5'))?>
        <?php echo $form->dropDownListRow($model, 'defaultNameserverSlave1', empty($model->defaultNameserverMaster) ? array() : $nameservers[$model->defaultNameserverMaster]['pairs'], array('empty'=>'','class'=>'span5 slave'))?>
        <?php
          $htmlOptions = array('empty'=>'','class'=>'span5 slave');
          if ($model->nameserversQty < 4) {
            $htmlOptions['disabled'] = 'disabled';
          }
        ?>
        <?php echo $form->dropDownListRow($model, 'defaultNameserverSlave2', empty($model->defaultNameserverMaster) ? array() : $nameservers[$model->defaultNameserverMaster]['pairs'], $htmlOptions)?>
        <?php echo $form->dropDownListRow($model, 'defaultNameserverSlave3', empty($model->defaultNameserverMaster) ? array() : $nameservers[$model->defaultNameserverMaster]['pairs'], $htmlOptions)?>
        <?php echo $form->dropDownListRow($model, 'minTtl', array(1=>Yii::t('common','{n} second|{n} seconds',array(1)),60=>Yii::t('common','{n} minute|{n} minutes',array(1)),1800=>Yii::t('common','{n} minute|{n} minutes',array(30)),3600=>Yii::t('common','{n} hour|{n} hours',array(1))), array('class'=>'span5'))?>
        <?php echo $form->radioButtonListRow($model, 'accessApi', Yii::app()->format->booleanFormat, array('separator'=>' '))?>
        <?php echo $form->textFieldRow($model, 'pricePerYear', array('class'=>'span1','prepend'=>CurrencyHelper::getCurrencySign()))?>
        <?php echo $form->textFieldRow($model, 'pricePerMonth', array('class'=>'span1','prepend'=>CurrencyHelper::getCurrencySign()))?>
        <?php echo $form->dropDownListRow($model, 'billing', $model->attributeLabelsBilling(), array('class'=>'span5'))?>
      </fieldset>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><s class="icon-save icon-white"></s> <?php echo Yii::t('common','Save')?></button>
        <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    <?php $this->endWidget()?>
  </div>
</div>
<?php

$this->cs->registerScript('ChangeNameserversQty',"
$('#" . CHtml::activeId($model,'nameserversQty') . "').bind('change',function()
{
  var value = $(this).val();
  if (value == 2) {
    $('#" . CHtml::activeId($model,'defaultNameserverSlave2') . ",#" . CHtml::activeId($model,'defaultNameserverSlave3') . "').val('').attr('disabled','disabled');
  }
  else {
    $('#" . CHtml::activeId($model,'defaultNameserverSlave2') . ",#" . CHtml::activeId($model,'defaultNameserverSlave3') . "').removeAttr('disabled');
  }
});
");

$this->cs->registerScript('ReloadPairs',"
$('#" . CHtml::activeId($model,'defaultNameserverMaster') . "').bind('change',function()
{
  var form = $('#" . get_class($model) . "');
  var pairs = $('.slave',form);
  pairs.empty().append('<option value selected></option>');
  var id = $(this).val();
  if (id) {
    $.ajax({
      url: '" . $this->createUrl('ajax',array('ajax'=>'ajaxActionSlaveReload')) . "',
      data: { id: id },
      type: 'get',
      dataType: 'json',
      cache: false,
      success: function(jdata)
      {
        for (i in jdata) {
          pairs.append($('<option />').prop('value',i).text(jdata[i]));
        }
        var selected = 1;
        pairs.each(function()
        {
          if (!$(this).attr('disabled')) {
            $(this).find('option:selected').removeAttr('selected');
            $(this).find('option:eq(' + selected + ')').attr('selected','selected');
            selected++;
          }
        });
      }
    });
  }
});
");
