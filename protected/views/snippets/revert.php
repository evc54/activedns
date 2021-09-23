<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/revert.php
  Document type : PHP script file
  Created at    : 03.02.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Revert to primary language invitation
*/?>
<?php $message = Yii::app()->user->getFlash('revertToSourceLanguage')?>
<div class="revert" style="display: none;">
  <button type="button">&times;</button>
  <?php echo $message?>
</div>
<?php $this->cs->registerScript('LanguageRevertFunctions',"
  setTimeout(function()
  {
    $('div.revert').slideDown().find('button').click(function()
    {
      $(this).parent().slideUp();
    });
  },300);
")?>
