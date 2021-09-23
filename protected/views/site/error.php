<?php
/**
  Project       : ActiveDNS
  Document      : views/site/error.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Site error template
*/?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <?php $this->beginWidget('bootstrap.widgets.TbHeroUnit', array(
        'heading'=>$code,
        'htmlOptions'=>array(
          'class'=>'error',
        ),
      ))?>
        <p class="error"><?php echo $message?></p>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
