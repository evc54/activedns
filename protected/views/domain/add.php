<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/add.php
  Document type : PHP script file
  Created at    : 06.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain add
*/?>
<div class="container">
  <div class="row">
    <div class="span7 offset3">
      <h3><?php echo Yii::t('domain','Add domains')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span6 offset3">
      <form method="post">
        <div class="control-group">
          <label class="control-label" for="domains"><?php echo Yii::t('domain','Domain names')?></label>
          <div class="controls">
            <textarea name="domains" id="domains" class="input-block-level" rows="5"></textarea>
            <span class="help-block"><?php echo Yii::t('domain','Enter domain names delimited by commas, spaces or new lines, without www or something like it')?></span>
            <span class="help-block"><?php echo Yii::t('domain','For example, {example1} or {example2}',array('{example1}'=>CHtml::tag('strong',array(),'example.com'),'{example2}'=>CHtml::tag('strong',array(),'subdomain.example.com')))?></span>
          </div>
        </div>
        <?php if (NameServerAlias::model()->filterByUser()->count()):?>
        <div class="control-group">
          <label class="control-label" for="templates"><?php echo Yii::t('domain','Choose nameservers for new domains')?></label>
          <div class="controls">
            <?php $this->widget('application.extensions.eselect2.ESelect2',array(
              'htmlOptions'=>array(
                'id'=>'nameservers',
                'multiple'=>false,
                'placeholder'=>Yii::t('domain','Default nameservers'),
              ),
              'options'=>array(
                'width'=>'100%',
                'allowClear'=>true,
              ),
              'name'=>'nameservers',
              'value'=>'',
              'data'=>NameServerAlias::model()->filterByUser()->getList(),
            ))?>
          </div>
        </div>
        <?php endif?>
        <div class="control-group">
          <label class="control-label"> </label>
          <div class="controls">
            <label class="checkbox" for="services">
              <input type="checkbox" name="services" id="services">
              <?php echo Yii::t('domain','Check for common services like www, ftp, mail etc.')?>
            </label>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="templates"><?php echo Yii::t('domain','Apply templates')?></label>
          <div class="controls">
            <?php $this->widget('application.extensions.eselect2.ESelect2',array(
              'htmlOptions'=>array(
                'id'=>'templates',
                'multiple'=>true,
              ),
              'options'=>array(
                'width'=>'100%',
              ),
              'name'=>'templates',
              'value'=>'',
              'data'=>CHtml::listData(Template::model()->select()->findAll(),'id','name'),
            ))?>
            <span class="help-block"><?php echo Yii::t('domain','Select templates you want to apply for new domains')?></span>
          </div>
        </div>
        <div>
          <button type="submit" id="add" name="add" class="btn btn-primary" data-loading-text="<?php echo Yii::t('domain','Processing...')?>"><?php echo Yii::t('common','Add')?></button>
          <a class="btn btn-link" href="<?php echo $this->createUrl('index')?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $this->cs->registerScript('submitButtonToggle',"$('#add').click(function(e){ $(this).button('loading'); });")?>
