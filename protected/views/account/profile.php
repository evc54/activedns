<?php
/**
  Project       : ActiveDNS
  Document      : views/account/profile.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account profile
*/
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'   => get_class($model),
    'type' => 'horizontal',
))?>
<div class="container">
  <div class="row">
    <div class="span12">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
    </div>
  </div>
  <div class="row">
    <fieldset class="span6">
      <legend><?php echo Yii::t('account','Account profile')?></legend>
      <?php echo $form->dropDownListRow($model, 'language', Yii::app()->params['languages'], array('empty'=>'-- ' . Yii::t('common','default') . ' --'))?>
      <?php echo $form->dropDownListRow($model, 'dateFormat', Yii::app()->params['dateFormats'], array('empty'=>'-- ' . Yii::t('common','default') . ' --'))?>
      <?php echo $form->dropDownListRow($model, 'timeFormat', Yii::app()->params['timeFormats'], array('empty'=>'-- ' . Yii::t('common','default') . ' --'))?>
      <?php echo $form->dropDownListRow($model, 'timeZone', $this->translateTimeZones(Yii::app()->params['timeZones']), array('empty'=>'-- ' . Yii::t('common','default') . ' --','class'=>'input-block-level'))?>
      <?php echo $form->dropDownListRow($model, 'statisticTimeFormat', Yii::app()->params['statisticTimeFormats'], array('empty'=>'-- ' . Yii::t('common','default') . ' --'))?>
    </fieldset>
    <fieldset class="span6">
      <legend><?php echo Yii::t('account','E-mail and password')?></legend>
      <?php echo $form->textFieldRow($model,'realname',array('class'=>'input-block-level'))?>
      <div class="control-group<?php echo $emailError ? ' error' : ''?>">
        <label class="control-label" for="email"><?php echo Yii::t('account','Change e-mail')?></label>
        <div class="controls">
          <input type="text" name="email" id="email" class="input-block-level" value="<?php echo $email?>">
          <span class="help-block"><?php echo Yii::t('account','A confirmation letter will be sent to old mailbox')?></span>
        </div>
      </div>
      <?php echo $form->passwordFieldRow($model,'newPassword',array('class'=>'span2'))?>
      <?php echo $form->passwordFieldRow($model,'newPasswordConfirm',array('class'=>'span2'))?>
    </fieldset>
  </div>
  <div class="row">
    <fieldset class="span6">
      <legend><?php echo Yii::t('account','Notification options')?></legend>
      <?php echo $form->dropDownListRow($model,'expireNotify',array(
        User::NOTIFY_EVERY_HOUR=>Yii::t('user','Every hour'),
        User::NOTIFY_EVERY_FOUR_HOURS=>Yii::t('user','Every 4 hours'),
        User::NOTIFY_EVERY_TWELVE_HOURS=>Yii::t('user','Every 12 hours'),
        User::NOTIFY_DAILY=>Yii::t('user','Daily'),
        User::NOTIFY_EVERY_THREE_DAYS=>Yii::t('user','Every three days'),
        User::NOTIFY_EVERY_FIVE_DAYS=>Yii::t('user','Every five days'),
        User::NOTIFY_WEEKLY=>Yii::t('user','Weekly'),
        User::NOTIFY_DISABLED=>Yii::t('user','Disable notification'),
      ),array('class'=>'input-block-level','empty'=>'-- ' . Yii::t('common','default') . ' --'))?>
      <?php echo $form->dropDownListRow($model,'alertNotify',array(
        User::NOTIFY_ONE_TIME=>Yii::t('user','Once as happened'),
        User::NOTIFY_EVERY_HOUR=>Yii::t('user','Every hour'),
        User::NOTIFY_EVERY_FOUR_HOURS=>Yii::t('user','Every 4 hours'),
        User::NOTIFY_EVERY_TWELVE_HOURS=>Yii::t('user','Every 12 hours'),
        User::NOTIFY_DAILY=>Yii::t('user','Daily'),
        User::NOTIFY_WEEKLY=>Yii::t('user','Weekly'),
        User::NOTIFY_DISABLED=>Yii::t('user','Disable notification'),
      ),array('class'=>'input-block-level','empty'=>'-- ' . Yii::t('common','default') . ' --'))?>
    </fieldset>
    <fieldset class="span6">
      <legend><?php echo Yii::t('account','Start of authority defaults')?></legend>
      <?php echo $form->textFieldRow($model,'soaHostmaster',array('class'=>'input-block-level','hint'=>Yii::t('domain','E-mail of host master')))?>
      <?php echo $form->dropDownListRow($model,'soaMinimum',Yii::app()->user->getAvailableTtl(),array('class'=>'input-block-level','hint'=>Yii::t('domain','Minimum time-to-live for unspecified resource records')))?>
      <?php
        $error = $model->getError('soaRefresh');
        $value = Zone::model()->getSuffixValue($model->soaRefresh);
      ?>
      <div class="control-group<?php echo $error ? ' error' : ''?>">
        <label class="control-label" for="soa-refresh"><?php echo Yii::t('domain','Refresh')?></label>
        <div class="controls">
        <?php
          echo CHtml::textField('soaRefresh', ceil($model->soaRefresh / $value), array('class'=>'input-small','id'=>'soa-refresh'));
          echo CHtml::dropDownList('soaRefreshMultiplier', $value, Zone::model()->getTtlSuffix(),array('id'=>'soa-refresh-multiplier','class'=>'input-large','style'=>'margin-left: 5px;'));
          echo $error ? CHtml::tag('span',array('class'=>'help-inline'),$error) : CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Time when the slave will try to refresh a zone from the master'));
        ?>
        </div>
      </div>
      <?php
        $error = $model->getError('soaRetry');
        $value = Zone::model()->getSuffixValue($model->soaRetry);
      ?>
      <div class="control-group<?php echo $error ? ' error' : ''?>">
        <label class="control-label" for="soa-retry"><?php echo Yii::t('domain','Retry')?></label>
        <div class="controls">
        <?php
          echo CHtml::textField('soaRetry', ceil($model->soaRetry / $value), array('class'=>'input-small','id'=>'soa-retry'));
          echo CHtml::dropDownList('soaRetryMultiplier', $value, Zone::model()->getTtlSuffix(),array('id'=>'soa-retry-multiplier','class'=>'input-large','style'=>'margin-left: 9px;'));
          echo $error ? CHtml::tag('span',array('class'=>'help-inline'),$error) : CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Defines the time between retries if the slave (secondary) fails to contact the master when refresh (above) has expired'));
        ?>
        </div>
      </div>
      <?php
        $error = $model->getError('soaExpire');
        $value = Zone::model()->getSuffixValue($model->soaExpire);
      ?>
      <div class="control-group<?php echo $error ? ' error' : ''?>">
        <label class="control-label" for="soa-expire"><?php echo Yii::t('domain','Expiry')?></label>
        <div class="controls">
        <?php
          echo CHtml::textField('soaExpire', ceil($model->soaExpire / $value), array('class'=>'input-small','id'=>'soa-expire'));
          echo CHtml::dropDownList('soaExpireMultiplier', $value, Zone::model()->getTtlSuffix(),array('id'=>'soa-expire-multiplier','class'=>'input-large','style'=>'margin-left: 9px;'));
          echo $error ? CHtml::tag('span',array('class'=>'help-inline'),$error) : CHtml::tag('p',array('class'=>'help-block'),Yii::t('domain','Indicates when a zone data is no longer authoritative'));
        ?>
        </div>
      </div>
    </fieldset>
  </div>
  <div class="row">
    <div class="span12 form-actions">
      <button class="btn btn-primary"><?php echo Yii::t('common','Save')?></button>
    </div>
  </div>
</div>
<?php $this->endWidget()?>
