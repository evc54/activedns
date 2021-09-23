<?php
/**
  Project       : ActiveDNS
  Document      : views/news/list.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News list  partial view file
*/
?>
<?php if (!empty($data->currentLanguageContent)):?>
<div class="news-entry">
  <h5><?php echo $data->currentLanguageContent->title?></h5>
  <p><?php echo nl2br($data->currentLanguageContent->announce)?></p>
  <div class="news-author">
    <small>
      <?php echo Yii::app()->format->formatDatetime($data->publish)?>
      <?php echo $data->author ? ' &mdash; ' . $data->author->realname : ''?>
    </small>
  </div>
  <a href="<?php echo $this->createUrl('news/read',array('id'=>$data->id))?>"><?php echo Yii::t('news','Read more')?> <s class="icon-caret-right"></s></a>
</div>
<?php endif?>
