<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/snippets/page/selector.php
  Document type : PHP script file
  Created at    : 02.02.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domains management page selector dropdown menu entries
*/?>
<a class="dropdown-toggle" data-toggle="dropdown" href="#">
<?php if ($pageSize == 'all'):?>
<?php echo Yii::t('domain','Show all domains on page')?> <span class="caret"></span>
<?php else:?>
<?php echo Yii::t('domain','Show {n} domain on page|Show {n} domains on page',array($pageSize))?> <span class="caret"></span>
<?php endif?>
</a>
