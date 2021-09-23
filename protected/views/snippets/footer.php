<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/footer.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Footer template
*/?>
<footer>
  <div class="container">
    <span class="copyright">© 2011-2015 <?php echo Yii::app()->name?></span>
    <span class="developer">
      <a href="http://viratechnologies.ru/" target="_blank">
        <img src="<?php echo $this->imageUrl('vira.png')?>" alt="Vira Technologies">
      </a>
    </span>
    <span class="service">
      <?php if (!empty(Yii::app()->params['social']['twitter'])):?>
      <a target="_blank" class="social-links" title="<?php echo Yii::t('common','Follow us in Twitter')?>" href="<?php echo Yii::app()->params['social']['twitter']?>"><s class="icon-twitter-sign"></s></a>
      <?php endif;?>
      <?php if (!empty(Yii::app()->params['social']['facebook'])):?>
      <a target="_blank" class="social-links" title="<?php echo Yii::t('common','Like us in Facebook')?>" href="<?php echo Yii::app()->params['social']['facebook']?>"><s class="icon-facebook-sign"></s></a>
      <?php endif;?>
      <div style="position: relative; top: 7px; display: inline;">
        <div class="g-plusone" data-href="<?php echo CHtml::encode($this->createAbsoluteUrl('/site/index')); ?>" data-size="standard" data-expandTo="top"></div>
      </div>
      <a rel="noindex,nofollow" href="<?php echo $this->createUrl('/site/terms')?>"><?php echo Yii::t('footer','Terms and conditions')?></a>&nbsp;|&nbsp;<a rel="noindex,nofollow" href="<?php echo $this->createUrl('/site/contact')?>"><?php echo Yii::t('footer','Contacts')?></a>
    </span>
  </div>
</footer>
