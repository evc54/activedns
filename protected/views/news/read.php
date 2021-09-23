<?php
/**
  Project       : ActiveDNS
  Document      : views/news/read.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News read page
*/
?>
<div class="container">
  <div class="row"><div class="span12"></div></div>
  <div class="row">
    <div class="span8">
    <?php if (!empty($model->currentLanguageContent)):?>
      <h3><?php echo $model->currentLanguageContent->title?></h3>
      <?php if ($model->currentLanguageContent->concat):?>
      <p><?php echo nl2br($model->currentLanguageContent->announce)?></p>
      <?php endif?>
      <p><?php echo nl2br($model->currentLanguageContent->fulltext)?></p>
      <div class="news-author">
        <small>
          <?php echo Yii::app()->format->formatDatetime($model->publish)?>
          <?php echo $model->author ? ' &mdash; ' . $model->author->realname : ''?>
        </small>
      </div>
    <?php else:?>
      <h3><?php echo Yii::t('news','Sorry, no translation to "{language}" language for this news',array(
        '{language}'=>empty(Yii::app()->params['languages'][Yii::app()->language]) ? Yii::app()->language : Yii::app()->params['languages'][Yii::app()->language],
      ))?></h3>
    <?php endif?>
    </div>
    <?php if (!empty($more['previous']) || !empty($more['next'])):?>
    <div class="span4">
      <h4 style="margin-top: 20px;"><?php echo Yii::t('dashboard','More news')?></h4>
      <?php if (!empty($more['previous'])):?>
      <?php echo $this->renderPartial('list',array(
        'data'=>$more['previous'],
      ))?>
      <?php endif?>
      <?php if (!empty($more['next'])):?>
      <?php echo $this->renderPartial('list',array(
        'data'=>$more['next'],
      ))?>
      <?php endif?>
    </div>
    <?php endif?>
  </div>
</div>
