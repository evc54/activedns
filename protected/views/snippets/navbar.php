<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/navbar.php
  Document type : PHP script file
  Created at    : 02.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Top navigation bar for unauthorized users snippet
*/
?>
<div class="navbar navbar-static-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="<?php echo $this->createAbsoluteUrl('/site/index')?>"><img src="<?php echo $this->imageUrl('logo-sm.png')?>" alt="ActiveDNS" /></a>
      <ul class="nav pull-right">
        <?php if (Yii::app()->user->isGuest): ?>
        <li><a href="#signup-modal" data-toggle="modal"><s class="icon-thumbs-up"></s> <?php echo Yii::t('menu','Get an account')?></a></li>
        <li class="divider-vertical"></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><s class="icon-signin"></s> <?php echo Yii::t('menu','Sign in')?> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li>
              <div style="padding:20px 20px 0;">
                <?php $this->renderPartial('//snippets/signin',array(
                  'model'=>new Credentials,
                ))?>
              </div>
            </li>
          </ul>
        </li>
        <?php else: ?>
        <li><a href="<?php echo $this->createUrl('panel/index')?>"><s class="icon-dashboard"></s> <?php echo Yii::t('menu','Dashboard')?></a></li>
        <li class="divider-vertical"></li>
        <li><a href="<?php echo $this->createUrl('site/signout')?>"><s class="icon-signout"></s> <?php echo Yii::t('menu','Log out')?></a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>
<?php $this->renderPartial('//snippets/signup')?>
