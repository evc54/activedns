<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/menu/admin.php
  Document type : PHP script file
  Created at    : 05.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Top administrator menu snippet
*/
?>
<?php $this->beginContent('//snippets/menus/common')?>
<li class="<?php echo in_array($this->id,array('user','nameserver','config','plan','info')) ? 'active ' : ''?>dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" href="#"><s class="icon-cogs"></s> <?php echo Yii::t('menu','Manage')?> <b class="caret"></b></a>
  <ul class="dropdown-menu">
    <li class="nav-header"><?php echo Yii::t('menu','ActiveDNS management')?></li>
    <li>
      <a href="<?php echo $this->createUrl('info/index')?>"><s class="icon-bullhorn"></s> <?php echo Yii::t('menu','News')?>
      <?php if (!$this->emptyCounter('totalNewsQty')):?>
        <span class="badge badge-inline"><?php echo $this->getCounter('totalNewsQty')?></span>
      <?php endif?>
      </a>
    </li>
    <li>
      <a href="<?php echo $this->createUrl('user/index')?>"><s class="icon-group"></s> <?php echo Yii::t('menu','Users')?>
      <?php if (!$this->emptyCounter('totalUsersQty')):?>
        <span class="badge badge-inline"><?php echo $this->getCounter('totalUsersQty')?></span>
      <?php endif?>
      </a>
    </li>
    <li>
      <a href="<?php echo $this->createUrl('template/common')?>"><s class="icon-bookmark-empty"></s> <?php echo Yii::t('menu','Common templates')?>
      <?php if (!$this->emptyCounter('commonTemplatesQty')):?>
        <span class="badge badge-inline"><?php echo $this->getCounter('commonTemplatesQty')?></span>
      <?php endif?>
      </a>
    </li>
    <li class="divider"></li>
    <li class="dropdown-submenu">
      <a tabindex="-1" href="#"><s class="icon-cog"></s> <?php echo Yii::t('menu','Configuration')?></a>
      <ul class="dropdown-menu">
        <li><a href="<?php echo $this->createUrl('config/index')?>"><s class="icon-cog"></s> <?php echo Yii::t('menu','Site configuration')?></a></li>
        <li>
          <a href="<?php echo $this->createUrl('nameserver/index')?>"><s class="icon-hdd"></s> <?php echo Yii::t('menu','Nameservers')?>
          <?php if (!$this->emptyCounter('totalNameserversQty')):?>
            <span class="badge badge-inline"><?php echo $this->getCounter('totalNameserversQty')?></span>
          <?php endif?>
          </a>
        </li>
        <li>
          <a href="<?php echo $this->createUrl('plan/index')?>"><s class="icon-money"></s> <?php echo Yii::t('menu','Pricing plans')?>
          <?php if (!$this->emptyCounter('totalPlansQty')):?>
            <span class="badge badge-inline"><?php echo $this->getCounter('totalPlansQty')?></span>
          <?php endif?>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</li>
<?php $this->endContent()?>
