<?php
/**
  Project       : ActiveDNS
  Document      : views/nameserver/update.php
  Document type : PHP script file
  Created at    : 20.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameserver update page
*/
?>
<div class="container">
  <div class="span10 offset1">
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>get_class($model),
      'type'=>'horizontal',
    ))?>
      <fieldset>
        <legend><?php echo $model->isNewRecord ? Yii::t('nameserver','New nameserver') : Yii::t('nameserver','Update nameserver ID {id}',array('{id}'=>$model->id))?></legend>
        <?php echo $form->dropDownListRow($model, 'status', $model->attributeLabelsStatus(), array('class'=>'span5'))?>

        <?php echo $form->textFieldRow($model, 'name', array('class'=>'span5'))?>
        <?php echo $form->textAreaRow($model, 'address', array('rows'=>4,'class'=>'span5','hint'=>Yii::t('nameserver','IP addresses delimited by commas or new lines')))?>
        <?php echo $form->textAreaRow($model, 'publicAddress', array('rows'=>4,'class'=>'span5','hint'=>Yii::t('nameserver','IP addresses delimited by commas or new lines')))?>
        <?php echo $form->dropDownListRow($model, 'type', $model->attributeLabelsType(), array('class'=>'span5'))?>
        <?php echo $form->dropDownListRow($model, 'idNameServerPair', $model->type == NameServer::TYPE_SLAVE ? $pairs : array(''=>''), array('class'=>'span5'))?>
        <?php echo $form->textFieldRow($model, 'token', array('class'=>'span5'))?>
        <?php echo $form->uneditableRow($model, 'lastStatUpload', array('class'=>'span5'))?>
      </fieldset>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><s class="icon-save icon-white"></s> <?php echo Yii::t('common','Save')?></button>
        <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    <?php $this->endWidget()?>
  </div>
</div>
<?php

$this->cs->registerScript('ReloadPairs',"
$('#" . CHtml::activeId($model,'type') . "').bind('change',function()
{
  var pair = $('#" . CHtml::activeId($model,'idNameServerPair') . "');
  var type = $(this).val();
  if (type == " . NameServer::TYPE_SLAVE . ") {
    var id = " . ($model->isNewRecord ? 'null' : ("'" . $model->id . "'")) . ";
    $.ajax({
      url: '" . $this->createUrl('ajax',array('ajax'=>'ajaxActionPairReload')) . "',
      data: { type: type, id: id },
      type: 'get',
      dataType: 'json',
      success: function(jdata)
      {
        pair.empty();
        for (i in jdata) {
          pair.append($('<option />').prop('value',i).text(jdata[i]));
        }
      }
    });
  }
  else {
    pair.empty();
    $('<option value=0 selected></option>').appendTo(pair);
  }
});
");
