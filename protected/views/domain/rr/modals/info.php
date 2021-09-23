<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/info.php
  Document type : PHP script file
  Created at    : 26.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to update domain info
*/
?>
<div class="control-group">
  <div class="controls">
    <label class="checkbox">
      <?php echo CHtml::checkBox('allowAutoCheck', $model->allowAutoCheck, array('id'=>'info-allow-auto-check','uncheckValue'=>0));?>
      <?php echo $model->getAttributeLabel('allowAutoCheck');?>
    </label>
  </div>
</div>
<?php $htmlOptions = $model->allowAutoCheck ? array('readonly'=>'readonly') : array();?>
<?php $error = $model->getError('register');?>
<div class="control-group<?php echo ($error ? ' error' : '')?>">
  <?php echo CHtml::label(Yii::t('domain','Registered date'), 'info-register', array('class'=>'control-label'));?>
  <div class="controls">
    <?php echo CHtml::textField('register', Yii::app()->format->formatDate($model->register), CMap::mergeArray($htmlOptions,array('class'=>'input-small','id'=>'info-register','rel'=>$model->register,'data-datepicker'=>'datepicker','data-format'=>Yii::app()->format->dateFormat)));?>
    <?php echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';?>
  </div>
</div>
<?php $error = $model->getError('expire');?>
<div class="control-group<?php echo ($error ? ' error' : '')?>">
  <?php echo CHtml::label(Yii::t('domain','Expiration date'), 'info-expire', array('class'=>'control-label'));?>
  <div class="controls">
    <?php echo CHtml::textField('expire', Yii::app()->format->formatDate($model->expire), CMap::mergeArray($htmlOptions,array('class'=>'input-small','id'=>'info-expire','rel'=>$model->expire,'data-datepicker'=>'datepicker','data-format'=>Yii::app()->format->dateFormat)));?>
    <?php echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';?>
  </div>
</div>
<?php for ($i = 1; $i < 5; $i++):?>
<?php $error = $model->getError('ns' . $i);?>
<div class="control-group<?php echo ($error ? ' error' : '')?>">
  <?php echo CHtml::label(Yii::t('domain','Current nameserver #{n}',array($i)), 'info-ns-' . $i, array('class'=>'control-label'));?>
  <div class="controls">
    <?php echo CHtml::textField('ns' . $i, $model->getAttribute('ns' . $i), CMap::mergeArray($htmlOptions,array('class'=>'input-large','id'=>'info-ns-' . $i)));?>
    <?php echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';?>
  </div>
</div>
<?php endfor;?>
<?php $error = $model->getError('registrar');?>
<div class="control-group<?php echo ($error ? ' error' : '')?>">
  <?php echo CHtml::label(Yii::t('domain','Registrar'), 'info-registrar', array('class'=>'control-label'));?>
  <div class="controls">
    <?php echo CHtml::textField('registrar',$model->registrar, CMap::mergeArray($htmlOptions,array('class'=>'input-large','id'=>'info-registrar')));?>
    <?php echo $error ? CHtml::tag('span',array('class'=>'help-block'),$error) : '';?>
  </div>
</div>
