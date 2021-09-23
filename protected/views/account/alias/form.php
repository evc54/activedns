<?php
/**
  Project       : ActiveDNS
  Document      : views/account/alias/form.php
  Document type : PHP script file
  Created at    : 05.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameserver's aliases create/update form
*/?>
<form class="form-horizontal" id="form-add-alias">
  <?php for ($i = 1; $i <= $user->plan->nameserversQty; $i++):?>
  <?php if (!empty($nameserversNames[$aliasSource[$i]])):?>
  <div class="control-group<?php if (!empty($error['aliasNS'][$i])):?> error<?php endif?>">
    <label class="control-label"><?php echo $nameserversNames[$aliasSource[$i]]?></label>
    <div class="controls">
      <input type="text" name="aliasNS[<?php echo $i?>]" class="span3" value="<?php echo empty($aliasNS[$i]) ? '' : $aliasNS[$i]?>" placeholder="ns<?php echo $i?>.example.net" />
      <?php if (!empty($error['aliasNS'][$i])):?>
      <div class="help-block"><?php echo $error['aliasNS'][$i]?></div>
      <?php else:?>
      <div class="help-block"><?php echo Yii::t('nameserver','You have to create resource records pointed to addresses <strong>{address}</strong>',array('{address}'=>str_replace(array(" ","\r\n","\t","\n","\r"),', ',$nameserversAddresses[$aliasSource[$i]])))?></div>
      <?php endif?>
    </div>
  </div>
  <?php endif?>
  <?php endfor?>
  <?php if (!empty($usage)):?>
  <div class="control-group">
    <div class="controls">
      <div class="help-block"><?php echo Yii::t('nameserver','<strong>Warning!</strong> Changes affects {n} domain|<strong>Warning!</strong> Changes affects {n} domains',array($usage))?></div>
    </div>
  </div>
  <?php endif?>
</form>
