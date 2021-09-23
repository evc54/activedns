<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/nameserver.php
  Document type : PHP script file
  Created at    : 06.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain's nameserver select
*/?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('domain','Choose nameservers for domain {domain}',array('{domain}'=>$model->getDomainName()))?></h2>
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
        <div class="control-group">
          <label class="control-label" for="templates"><?php echo Yii::t('domain','Select nameservers')?></label>
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
              'value'=>$nameServerAliasID,
              'data'=>NameServerAlias::model()->filterByUser($model->idUser)->getList(),
            ))?>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label"> </label>
          <div class="controls">
            <label class="checkbox" for="apply">
              <input type="checkbox" name="apply" id="apply">
              <?php echo Yii::t('domain','Apply zone update')?>
            </label>
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-success"><s class="icon-ok icon-white"></s> <?php echo Yii::t('domain','Change')?></button>
          <a class="btn btn-link" href="<?php echo $returnUrl?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>
        <?php echo CHtml::hiddenField('returnUrl',$returnUrl)?>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
