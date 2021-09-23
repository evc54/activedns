<?php
/**
  Project       : ActiveDNS
  Document      : views/user/update.php
  Document type : PHP script file
  Created at    : 16.09.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : User's update page
*/
?>
<div class="container">
  <div class="span10 offset1">
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>get_class($model),
      'type'=>'horizontal',
    ))?>
    <fieldset>
      <legend><?php echo $model->isNewRecord ? Yii::t('user','New user') : Yii::t('user','Update user ID {id}',array('{id}'=>$model->id))?></legend>
      <?php echo $form->dropDownListRow($model, 'status', $model->attributeLabelsStatus(), array('class'=>'span5'))?>
      <?php echo $form->dropDownListRow($model, 'role', $model->attributeLabelsRole(), array('class'=>'span5'))?>
      <?php echo $form->dropDownListRow($model, 'idPricingPlan', CHtml::listData(PricingPlan::model()->findAll(),'id','title'), array('class'=>'span5'))?>
      <?php echo $form->dropDownListRow($model, 'billing', array(PricingPlan::BILLING_ANNUALLY=>Yii::t('pricingPlan','Annually'),PricingPlan::BILLING_MONTHLY=>Yii::t('pricingPlan','Monthly')), array('class'=>'span5'))?>
      <?php echo $form->textFieldRow($model, 'paidTill', array('class'=>'span2'))?>
      <?php echo $form->textFieldRow($model, 'email', array('class'=>'span5'))?>
      <?php echo $form->textFieldRow($model, 'realname', array('class'=>'span5'))?>
      <?php echo $form->passwordFieldRow($model, 'newPassword', array('class'=>'span5'))?>
      <?php echo $form->passwordFieldRow($model, 'newPasswordConfirm', array('class'=>'span5'))?>
      <?php echo $form->dropDownListRow($model, 'ns1', $masterNameservers, array('empty'=>'','class'=>'span5'))?>
      <?php echo $form->dropDownListRow($model, 'ns2', empty($model->ns1) ? array() : $nameservers[$model->ns1]['pairs'], array('empty'=>'','class'=>'span5 slave'))?>
      <?php
        $htmlOptions = array('empty'=>'','class'=>'span5 slave');
        if (!$model->plan || $model->plan->nameserversQty < 4) {
          $htmlOptions['disabled'] = 'disabled';
        }
      ?>
      <?php echo $form->dropDownListRow($model, 'ns3', empty($model->ns1) ? array() : $nameservers[$model->ns1]['pairs'], $htmlOptions)?>
      <?php echo $form->dropDownListRow($model, 'ns4', empty($model->ns1) ? array() : $nameservers[$model->ns1]['pairs'], $htmlOptions)?>
    </fieldset>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><s class="icon-save icon-white"></s> <?php echo Yii::t('common','Save')?></button>
      <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
    </div>
    <?php $this->endWidget()?>
  </div>
</div>
<?php

$this->cs->registerScript('PlanNameserversQty',"var nameserversQty=" . CJavaScript::encode($nameserversQty) . ";",  CClientScript::POS_END);

$this->cs->registerScript('ChangePlan',"
$('#" . CHtml::activeId($model,'idPricingPlan') . "').bind('change',function()
{
  var value = $(this).val();
  $('#" . CHtml::activeId($model,'ns1') . ",#" . CHtml::activeId($model,'ns2') . ",#" . CHtml::activeId($model,'ns3') . ",#" . CHtml::activeId($model,'ns4') . "').removeAttr('disabled');
  if (nameserversQty[value] < 4) {
    $('#" . CHtml::activeId($model,'ns3') . ",#" . CHtml::activeId($model,'ns4') . "').attr('disabled','disabled').val('');
  }
});
");

$this->cs->registerScript('ReloadPairs',"
$('#" . CHtml::activeId($model,'ns1') . "').bind('change',function()
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
