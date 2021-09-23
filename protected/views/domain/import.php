<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/import.php
  Document type : PHP script file
  Created at    : 29.06.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Import zone file view
*/
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <h3><?php echo Yii::t('domain','<small>Import zone file for domain</small> {domain}',array('{domain}'=>$model->name))?></h3>
    </div>
  </div>
  <div class="row">
    <div class="span3">
      <div class="well active-menu">
        <ul class="nav nav-list">
          <li><a href="<?php echo $this->createUrl('update',array('id'=>$model->id))?>"><s class="icon-arrow-left icon-black"></s> <?php echo Yii::t('domain','Back to zone editor')?></a></li>
        </ul>
      </div>
    </div>
    <div class="span9">
      <?php $this->widget('bootstrap.widgets.TbAlert')?>
      <?php if (!empty($error)):?>
      <div class="alert alert-block alert-error">
        <h4><?php echo Yii::t('error','An error occurred while processing')?></h4>
        <p><?php echo $error?></p>
      </div>
      <?php endif?>
      <?php if (empty($report) || !empty($error)):?>
      <form method="post" enctype="multipart/form-data">
        <fieldset>
          <legend><?php echo Yii::t('domain','Choose zone file for import')?></legend>
          <p>
            <input type="file" name="zonefile" value="" class="input-block-level" />
          </p>
          <button class="btn btn-primary" type="submit"><?php echo Yii::t('domain','Import')?></button>
        </fieldset>
      </form>
      <?php elseif (empty($error)):?>
      <?php if (isset($c)):?>
      <div class="alert alert-block alert-success">
        <h4><?php echo Yii::t('success','Operation successfully completed')?></h4>
        <p><?php echo Yii::t('success','{n} record imported|{n} records imported',array($c))?></p>
      </div>
      <?php endif?>
      <fieldset>
        <legend><?php echo Yii::t('domain','Import report')?></legend>
        <table class="import-report">
          <tbody>
            <?php foreach ($report as $line => $content):?>
            <tr class="<?php echo $content['report']?>">
              <td width="40" align="right" valign="top"><pre class="line-number"><?php echo $line + 1?></pre></td>
              <td valign="top"><pre><?php echo $content['origin']?></pre></td>
              <td width="170" valign="top"><?php echo $info[$content['report']]?></td>
            </tr>
            <?php endforeach?>
          </tbody>
        </table>
      </fieldset>
      <?php endif?>
    </div>
  </div>
</div>
