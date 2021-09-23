<?php
/**
  Project       : ActiveDNS
  Document      : views/support/reopen.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support ticket's reopen confirmation
*/
?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('ticket','Are you sure to reopen ticket?')?></h2>
    </div>
  </div>
  <div class="row">
    <div class="span10 offset1">
      <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>get_class($model),
        'type'=>'horizontal',
      ))?>
        <?php $this->renderPartial('detail',array(
          'model'=>$model,
        ))?>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary"><s class="icon-retweet icon-white"></s> <?php echo Yii::t('ticket','Reopen')?></button>
          <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
