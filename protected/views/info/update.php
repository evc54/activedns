<?php
/**
  Project       : ActiveDNS
  Document      : views/info/update.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News create/update page
*/
?>
<div class="container">
  <div class="span10 offset1">
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id'=>get_class($model),
      'type'=>'horizontal',
    ))?>
      <fieldset>
        <legend><?php echo $model->isNewRecord ? Yii::t('news','New news entry') : Yii::t('news','Update news entry ID {id}',array('{id}'=>$model->id))?></legend>
        <?php echo $form->checkBoxRow($model, 'public')?>
        <?php if ($model->author):?>
        <?php echo $form->uneditableRow($model, 'create', array('class'=>'span3'))?>
        <?php echo $form->uneditableRow($model, 'publish', array('class'=>'span3'))?>
        <?php echo $form->uneditableRow($model, 'update', array('class'=>'span3'))?>
        <?php echo $form->uneditableRow($model, 'idUser', array('class'=>'span3'))?>
        <?php endif?>
      </fieldset>
      <ul class="nav nav-tabs">
        <?php foreach (Yii::app()->params['languages'] as $code => $language):?>
        <li<?php if ($code == Yii::app()->language):?> class="active"<?php endif?>><a href="#<?php echo $code?>" data-toggle="tab"><?php echo $language?></a></li>
        <?php endforeach?>
      </ul>
      <div class="tab-content">
        <?php foreach (Yii::app()->params['languages'] as $code => $language):?>
        <?php $content = $model->getContent($code)?>
        <div class="tab-pane fade<?php if ($code == Yii::app()->language):?> active in<?php endif?>" id="<?php echo $code?>">
          <?php echo $form->textFieldRow($content,'title',array('class'=>'span5','id'=>'content_' . $code . '_title','name'=>'content[' . $code . '][title]'))?>
          <?php echo $form->textAreaRow($content,'announce',array('class'=>'span5','id'=>'content_' . $code . '_announce','name'=>'content[' . $code . '][announce]','rows'=>5))?>
          <?php echo $form->textAreaRow($content,'fulltext',array('class'=>'span5','id'=>'content_' . $code . '_fulltext','name'=>'content[' . $code . '][fulltext]','rows'=>10))?>
          <?php echo $form->checkBoxRow($content, 'concat',array('id'=>'content_' . $code . '_concat','name'=>'content[' . $code . '][concat]'))?>
        </div>
        <?php endforeach?>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><s class="icon-save icon-white"></s> <?php echo Yii::t('common','Save')?></button>
        <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
      </div>
    <?php $this->endWidget()?>
  </div>
</div>
