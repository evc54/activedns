<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/snippets/page/menu.php
  Document type : PHP script file
  Created at    : 02.02.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domains management page selector menu
*/?>
<?php foreach($this->getPagination() as $pageSize):?>
<li><a tabindex="-1" href="<?php echo $this->createUrl('index',array('size'=>$pageSize))?>">
<?php if($pageSize == 'all'):?>
<?php echo Yii::t('domain','Show all domains on page')?>
<?php else:?>
<?php echo Yii::t('domain','Show {n} domain on page|Show {n} domains on page',array($pageSize))?>
<?php endif?>
</a></li>
<?php endforeach?>
