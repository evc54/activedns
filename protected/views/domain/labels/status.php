<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/labels/status.php
  Document type : PHP script file
  Created at    : 06.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain label
*/?>
<span class="label label-<?php echo $model->getAttributeStatusClass()?>"><?php echo $model->getAttributeStatusLabel()?></span>
<?php if (empty($noTip)):?>
<a href="javascript:void(0)" class="context-help-status" rel="tooltip" data-original-title="<?php echo $model->getAttributeStatusHint()?>"><s class="icon-question-sign icon-black"></s></a>
<?php endif?>
