<?php
/**
  Project       : ActiveDNS
  Document      : views/config/index.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Site configuration editor
*/
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'   => get_class($model),
  'type' => 'horizontal',
))?>
<div class="container">
  <div class="row">
    <div class="span8 offset2">
      <h3><?php echo Yii::t('config','Site configuration')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span8 offset2">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
    </div>
  </div>
  <div class="row">
    <div class="span8 offset2">
      <fieldset>
        <legend><?php echo Yii::t('config','General options')?></legend>
      </fieldset>
      <div class="control-group">
        <label class="control-label" for="Config_AllowSignup"><?php echo Yii::t('config','Allow sign up')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('Config[AllowSignup]', Config::get('AllowSignup'), Yii::app()->format->booleanFormat, array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_PrimaryLanguage"><?php echo Yii::t('config','Primary language')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('Config[PrimaryLanguage]', Config::get('PrimaryLanguage'), Yii::app()->params['languages'], array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_PrimaryCurrency"><?php echo Yii::t('config','Primary currency')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('Config[PrimaryCurrency]', Config::get('PrimaryCurrency'), Yii::app()->params['currencies'], array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_NewAccountPlan"><?php echo Yii::t('config','New account pricing plan')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('Config[NewAccountPlan]', Config::get('NewAccountPlan'), CHtml::listData(PricingPlan::model()->findAll(), 'id', 'title'), array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_DateFormat"><?php echo Yii::t('config','Date format')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('Config[DateFormat]', Config::get('DateFormat'), Yii::app()->params['dateFormats'], array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_TimeFormat"><?php echo Yii::t('config','Time format')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('Config[TimeFormat]', Config::get('TimeFormat'), Yii::app()->params['timeFormats'], array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_TimeZone"><?php echo Yii::t('config','Time zone')?></label>
        <div class="controls">
          <?php echo CHtml::dropDownList('Config[TimeZone]', Config::get('TimeZone'), Yii::app()->params['timeZones'], array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_InvoicePrefix"><?php echo Yii::t('config','Gateway invoice prefix')?></label>
        <div class="controls">
          <?php echo CHtml::textField('Config[InvoicePrefix]', Config::get('InvoicePrefix'), array('class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_GoogleAnalyticsCode"><?php echo Yii::t('config','Google analytics code')?></label>
        <div class="controls">
          <?php echo CHtml::textArea('Config[GoogleAnalyticsCode]', Config::get('GoogleAnalyticsCode'), array('rows'=>15,'class'=>'input-block-level'))?>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="Config_CustomCounterCode"><?php echo Yii::t('config','Custom counter code')?></label>
        <div class="controls">
          <?php echo CHtml::textArea('Config[CustomCounterCode]', Config::get('CustomCounterCode'), array('rows'=>10,'class'=>'input-block-level'))?>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="span8 offset2">
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><s class="icon-save icon-white"></s> <?php echo Yii::t('common','Save')?></button>
        <a class="btn btn-link" href="<?php echo $this->createUrl('/panel/index')?>"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    </div>
  </div>
</div>
<?php $this->endWidget()?>
