<?php
/**
  Project       : ActiveDNS
  Document      : views/news/index.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News viewer index page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('news','News <small>browse</small>')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <?php $this->widget('bootstrap.widgets.TbListView',array(
        'id'=>'news',
        'dataProvider'=>$model->index(),
        'itemView'=>'list',
        'template'=>'{items}',
        'emptyText'=>CHtml::tag('span',array('class'=>'muted'),Yii::t('news','News not yet available')),
      ))?>
    </div>
  </div>
</div>
