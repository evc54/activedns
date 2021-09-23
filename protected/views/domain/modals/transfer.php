<form onsubmit="return false;">
  <div class="control-group<?php echo $model->hasErrors('address') ? ' error' : '';?>">
    <label class="control-label" for="transfer-address"><?php echo $model->getAttributeLabel('address');?></label>
    <div class="controls">
      <input type="text" name="address" class="input-large" id="transfer-address" value="<?php echo $model->address;?>" />
      <div class="help-block"><?php echo CHtml::error($model,'address');?></div>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <label class="checkbox">
        <?php echo CHtml::checkBox('allowNotify',$model->allowNotify,array('id'=>'transfer-allow-notify','uncheckValue'=>0));?>
        <?php echo $model->getAttributeLabel('allowNotify');?>
      </label>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <label class="checkbox">
        <?php echo CHtml::checkBox('allowTransfer',$model->allowNotify,array('id'=>'transfer-allow-transfer','uncheckValue'=>0));?>
        <?php echo $model->getAttributeLabel('allowTransfer');?>
      </label>
    </div>
  </div>
</form>
