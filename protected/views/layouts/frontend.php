<?php
/**
  Project       : ActiveDNS
  Document      : views/layouts/frontend.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Frontend layout
*/
?>
<?php $this->beginContent('//layouts/main')?>
  <noindex>
    <?php $this->renderPartial('//snippets/navbar')?>
  </noindex>
  <?php echo $content?>
  <noindex>
    <?php $this->renderPartial('//snippets/footer')?>
  </noindex>
<?php $this->endContent()?>
