<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/menu/common.php
  Document type : PHP script file
  Created at    : 04.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Common menu snippet
*/?>
<div class="navbar navbar-static-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand" href="<?php echo $this->createAbsoluteUrl('/site/index')?>" title="<?php echo Yii::app()->params['engine'] . ', v' . Yii::app()->params['version']?>"><img src="<?php echo $this->imageUrl('logo-sm.png')?>" alt="ActiveDNS" /></a>
      <ul class="nav">
        <li<?php echo in_array($this->id,array('panel')) ? ' class="active"' : ''?>>
          <a href="<?php echo $this->createUrl('/panel/index')?>"><s class="icon-dashboard"></s> <span class="hidden-mini"><?php echo Yii::t('menu','Dashboard')?></span></a>
        </li>
        <li<?php echo in_array($this->id,array('domain')) ? ' class="active"' : ''?>>
          <a<?php if (!$this->emptyCounter('totalDomainsQty')):?> class="counter"<?php endif?> href="<?php echo $this->createUrl('/domain/index')?>"><s class="icon-sitemap"></s> <span class="hidden-mini"><?php echo Yii::t('menu','Domains')?></span></a>
          <?php if (!$this->emptyCounter('totalDomainsQty')):?>
            <span class="badge badge-custom badge-domain"><?php echo $this->getCounter('totalDomainsQty')?></span>
          <?php endif?>
        </li>
        <li class="<?php echo in_array($this->id,array('events')) ? 'active ' : ''?>hidden-medium">
          <a<?php if (!$this->emptyCounter('unseenEventsQty')):?> class="counter"<?php endif?> href="<?php echo $this->createUrl('/events/index')?>"><s class="icon-warning-sign"></s> <?php echo Yii::t('menu','Events')?></a>
          <?php if (!$this->emptyCounter('unseenEventsQty')):?>
            <span class="badge badge-custom badge-event"><?php echo $this->getCounter('unseenEventsQty')?></span>
          <?php endif?>
        </li>
        <li class="<?php echo in_array($this->id,array('template')) ? 'active ' : ''?>hidden-medium">
          <a<?php if (!$this->emptyCounter('totalTemplatesQty')):?> class="counter"<?php endif?> href="<?php echo $this->createUrl('/template/index')?>"><s class="icon-bookmark"></s> <?php echo Yii::t('menu','Templates')?></a>
          <?php if (!$this->emptyCounter('totalTemplatesQty')):?>
            <span class="badge badge-custom badge-template"><?php echo $this->getCounter('totalTemplatesQty')?></span>
          <?php endif?>
        </li>
        <li class="dropdown hidden-max">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li>
            <a href="<?php echo $this->createUrl('/events/index')?>"><s class="icon-warning-sign"></s> <?php echo Yii::t('menu','Events')?></a>
            </li>
            <li>
              <a href="<?php echo $this->createUrl('/template/index')?>"><s class="icon-bookmark"></s> <?php echo Yii::t('menu','Templates')?></a>
            </li>
          </ul>
        </li>
        <?php echo $content?>
      </ul>
      <ul class="nav pull-right hidden-phone">
        <li class="<?php echo in_array($this->id,array('support')) ? 'active ' : ''?>hidden-phone">
          <a<?php if (!$this->emptyCounter('unseenSupportQty')):?> class="counter"<?php endif?> href="<?php echo $this->createUrl('support/index')?>"><s class="icon-comments-alt"></s> <span class="hidden-right"><?php echo Yii::t('menu','Get support')?></span></a>
          <?php if (!$this->emptyCounter('unseenSupportQty')):?>
            <span class="badge badge-custom badge-support"><?php echo $this->getCounter('unseenSupportQty')?></span>
          <?php endif?>
        </li>
        <li class="divider-vertical"></li>
        <li class="<?php echo in_array($this->id,array('account')) ? 'active ' : ''?>dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><s class="icon-user"></s> <span class="hidden-right"><?php echo Yii::t('menu','My account')?></span> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li class="nav-header"><?php echo Yii::app()->user->getAttribute('email')?></li>
            <li><a href="<?php echo $this->createUrl('account/index')?>"><s class="icon-briefcase"></s> <?php echo Yii::t('menu','Account options')?></a></li>
            <li><a href="<?php echo $this->createUrl('account/nameserver')?>"><s class="icon-hdd"></s> <?php echo Yii::t('menu','Nameserver options')?></a></li>
            <li><a href="<?php echo $this->createUrl('account/profile')?>"><s class="icon-wrench"></s> <?php echo Yii::t('menu','Profile')?></a></li>
            <li class="divider"></li>
            <li><a href="<?php echo $this->createUrl('site/signout')?>"><s class="icon-signout"></s> <?php echo Yii::t('menu','Log out')?></a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
