<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/signup.php
  Document type : PHP script file
  Created at    : 04.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Sign up modal window
*/
?>
<div class="modal hide fade" id="signup-modal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo Yii::t('site','Sign up')?></h3>
  </div>
  <div class="modal-body">
    <form class="form-horizontal" method="post" action="<?php echo $this->createUrl('/site/signup')?>">
      <div class="control-group">
        <label class="control-label"><?php echo Yii::t('site','Your e-mail address')?></label>
        <div class="controls">
          <input type="text" class="input-large" name="email" id="signup-email">
          <span style="display: none;" class="help-block" id="signup-error"><strong><?php echo Yii::t('site',"E-mail you're entered is not valid e-mail address")?></strong></span>
          <span class="help-block"><?php echo Yii::t('site','<strong>Note:</strong> all sensitive information regarding your account will be sent to this e-mail address')?></span>
        </div>
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-link" data-dismiss="modal" aria-hidden="true"><?php echo Yii::t('common','Cancel')?></a>
    <button id="signup-process" type="submit" class="btn btn-primary"><?php echo Yii::t('site','Continue')?> <s class="icon-chevron-right icon-white"></s></button>
  </div>
</div>
<?php

$this->cs->registerScript('emailChecker',"
$('#signup-process').click(function(e)
{
  e.preventDefault();
  var email = $('#signup-email');
  if (email.val().match(/^.+@.+\..+$/)) {
    email.closest('form').submit();
  }
  else {
    email.addClass('error');
    email.closest('.control-group').addClass('error');
    $('#signup-error').slideDown('fast');
  }
});
");
