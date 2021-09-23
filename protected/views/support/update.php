<?php
/**
  Project       : ActiveDNS
  Document      : views/support/update.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support ticket's update page
*/
?>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
  'id'=>get_class($model),
))?>
  <div class="container">
    <div class="row">
      <div class="span3">
        <div class="well active-menu">
          <ul class="nav nav-list">
            <li><a href="<?php echo $this->createUrl('index')?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('ticket','Back to tickets')?></a></li>
          </ul>
        </div>
        <div class="well active-menu">
          <ul class="nav nav-list">
            <li class="nav-header"><?php echo Yii::t('ticket','ticket #')?></li>
            <li><?php echo $model->id?></li>
            <li class="nav-header"><?php echo Yii::t('ticket','ticket status')?></li>
            <li><span class="label label-status-<?php echo strtolower($model->getStatusClass())?>"><?php echo $model->getAttributeLabelStatus()?></span></li>
            <li class="nav-header"><?php echo Yii::t('ticket','subject')?></li>
            <li><?php echo $model->subject?></li>
            <li class="nav-header"><?php echo Yii::t('ticket','created at')?></li>
            <li><?php echo $model->created ? Yii::app()->format->formatDatetime($model->created) : '&mdash;'?></li>
            <li class="nav-header"><?php echo Yii::t('ticket','last replied at')?></li>
            <li><?php echo $model->replied ? Yii::app()->format->formatDatetime($model->replied) : '&mdash;'?></li>
            <?php if (Yii::app()->user->getRole() == User::ROLE_ADMIN):?>
            <li class="nav-header"><?php echo Yii::t('ticket','author')?></li>
            <li><?php echo $model->author ? CHtml::link($model->author->email,$this->createUrl('user/update',array('id'=>$model->authorID))) : '&mdash;'?></li>
            <?php endif?>
          </ul>
        </div>
        <div class="well active-menu">
          <ul class="nav nav-list">
            <li class="nav-header"><?php echo Yii::t('common','actions')?></li>
            <?php if (Yii::app()->user->getRole() == User::ROLE_ADMIN && $model->status != SupportTicket::STATUS_PROCESS):?>
            <li><a href="<?php echo $this->createUrl('process',array('id'=>$model->id))?>"><s class="icon-ok-circle icon-black"></s> <?php echo Yii::t('ticket','Set processing status')?></a></li>
            <?php endif?>
            <?php if ($model->status != SupportTicket::STATUS_CLOSED):?>
            <li><a href="<?php echo $this->createUrl('close',array('id'=>$model->id))?>"><s class="icon-remove-circle icon-black"></s> <?php echo Yii::t('ticket','Close ticket')?></a></li>
            <?php else:?>
            <li><a href="<?php echo $this->createUrl('reopen',array('id'=>$model->id))?>"><s class="icon-retweet icon-black"></s> <?php echo Yii::t('ticket','Reopen ticket')?></a></li>
            <?php endif?>
          </ul>
        </div>
      </div>
      <div class="span9">
        <ul class="nav nav-tabs">
          <li<?php if (!Yii::app()->request->isPostRequest):?> class="active"<?php endif?>><a href="#history" data-toggle="tab"><?php echo Yii::t('ticket','History')?></a></li>
          <?php if ($model->status != SupportTicket::STATUS_CLOSED):?>
          <li<?php if (Yii::app()->request->isPostRequest):?> class="active"<?php endif?>><a href="#reply" data-toggle="tab"><?php echo Yii::t('ticket','Reply')?></a></li>
          <?php endif?>
        </ul>
        <div class="tab-content">
          <div id="history" class="tab-pane fade<?php if (!Yii::app()->request->isPostRequest):?> active in<?php endif?>">
            <?php foreach ($model->replies as $n => $history):?>
            <fieldset class="history">
              <div class="control-group">
                <label class="control-label"><?php if ($n == 0) { echo Yii::t('ticket','Message'); } else { echo Yii::t('ticket','Reply #'); echo ' '; echo $n; }?></label>
                <div class="controls">
                  <blockquote><?php echo nl2br($history->text)?><small><?php echo Yii::t('ticket','Sent at {datetime} by {username}',array('{datetime}'=>Yii::app()->format->formatDatetime($history->created),'{username}'=>($history->author !== null ? ($history->author->realname ? $history->author->realname : $history->author->email) : ('ID ' . $history->authorID))))?></small></blockquote>
                </div>
              </div>
            </fieldset>
            <?php endforeach?>
          </div>
          <?php if ($model->status != SupportTicket::STATUS_CLOSED):?>
          <div id="reply" class="tab-pane fade<?php if (Yii::app()->request->isPostRequest):?> active in<?php endif?>">
            <fieldset>
              <?php echo $form->textAreaRow($reply, 'text', array('rows'=>7,'class'=>'span5'))?>
            </fieldset>
            <button type="submit" class="btn btn-primary"><?php echo Yii::t('ticket','Send & Back to tickets')?></button>
            <button type="submit" class="btn btn-primary" name="returnBack"><?php echo Yii::t('ticket','Send & Return to ticket')?></button>
          </div>
          <?php endif?>
        </div>
      </div>
    </div>
  </div>
<?php $this->endWidget()?>
