<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/labels/name.php
  Document type : PHP script file
  Created at    : 07.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain name label
*/?>
<?php echo $model->name?>
<a href="javascript:void(0)" class="context-help-status hidden-medium" rel="tooltip" data-original-title="<?php echo implode(", ",$model->domainNameservers)?>"><s class="icon-hdd icon-black"></s></a>
