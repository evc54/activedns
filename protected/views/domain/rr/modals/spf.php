<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/modals/spf.php
  Document type : PHP script file
  Created at    : 12.07.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Content of modal dialog to add resource record type TXT
*/

$attributes = array(
  'model' => $model,
  'rr'    => $rr,
  'id'    => $id,
  'quick' => true,
);

$this->renderPartial('/domain/rr/modals/snippets/host',CMap::mergeArray($attributes,array('help'=>Yii::t('domain','Text entry host. Enter <strong>@</strong> for whole domain'))));
?>
<div class="control-group">
  <div class="controls">
    <label class="checkbox">
      <input type="hidden" name="a" value="0" id="<?php echo $id?>-a-0" />
      <input type="checkbox" name="a" value="1" id="<?php echo $id?>-a-1"<?php echo Yii::app()->request->getParam('a') ? ' checked="checked"' : ''?> />
      <?php echo Yii::t('domain','Allow domain A-type resource records')?>
    </label>
  </div>
</div>
<div class="control-group">
  <div class="controls">
    <label class="checkbox">
      <input type="hidden" name="mx" value="0" id="<?php echo $id?>-mx-0" />
      <input type="checkbox" name="mx" value="1" id="<?php echo $id?>-mx-1"<?php echo Yii::app()->request->getParam('mx') ? ' checked="checked"' : ''?> />
      <?php echo Yii::t('domain','Allow domain MX-type resource records')?>
    </label>
  </div>
</div>
<div class="control-group">
  <label class="control-label"><?php echo Yii::t('domain','Include SPF records from other domains')?></label>
  <div class="controls">
    <textarea name="include" class="input-large" rows="3" id="<?php echo $id?>-include"><?php echo Yii::app()->request->getParam('address')?></textarea>
    <div class="help-block"><?php echo Yii::t('domain','Entries must be delimited by new lines')?></div>
  </div>
</div>
<div class="control-group">
  <label class="control-label"><?php echo Yii::t('domain','Allow IPv4, IPv6 or host names')?></label>
  <div class="controls">
    <textarea name="address" class="input-large" rows="5" id="<?php echo $id?>-address"><?php echo Yii::app()->request->getParam('address')?></textarea>
    <div class="help-block"><?php echo Yii::t('domain','Entries must be delimited by new lines')?></div>
  </div>
</div>
<div class="control-group">
  <div class="controls">
    <label class="checkbox">
      <input type="hidden" name="all" value="0" id="<?php echo $id?>-all-0" />
      <input type="checkbox" name="all" value="1"<?php echo Yii::app()->request->getParam('all') > 0 ? ' checked="checked"' : ''?> />
      <?php echo Yii::t('domain','Allow all')?>
    </label>
  </div>
</div>
<div class="control-group">
  <div class="controls">
    <label class="checkbox">
      <input type="checkbox" name="all" value="-1" id="<?php echo $id?>-all--1"<?php echo Yii::app()->request->getParam('all') < 0 ? ' checked="checked"' : ''?> />
      <?php echo Yii::t('domain','Deny all')?>
    </label>
  </div>
</div>
<?php
$this->renderPartial('/domain/rr/modals/snippets/ttl',$attributes);
?>
<div class="control-group">
  <div class="controls">
    <label class="checkbox">
      <input type="hidden" name="replace" value="0" id="<?php echo $id?>-replace-0" />
      <input type="checkbox" name="replace" value="1" id="<?php echo $id?>-replace-1"<?php echo Yii::app()->request->getParam('replace') ? ' checked="checked"' : ''?> />
      <?php echo Yii::t('domain','Replace existing SPF record')?>
    </label>
  </div>
</div>
