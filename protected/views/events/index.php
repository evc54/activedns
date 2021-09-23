<?php
/**
  Project       : ActiveDNS
  Document      : views/events/index.php
  Document type : PHP script file
  Created at    : 08.01.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Events viewer index page
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('events','Events <small>browse</small>')?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span12">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
      <?php $this->renderPartial('grid',array(
        'model'=>$model,
      ))?>
    </div>
  </div>
</div>
<?php $this->cs->registerScriptFile($this->scriptUrl('filter-clear-fields'))?>
<?php $this->cs->registerScript('clearFilterFieldsInit',"
setClearFields();
function afterAjaxUpdate()
{
  setClearFields();
}
")?>
