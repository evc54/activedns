<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/client.php
  Document type : PHP script file
  Created at    : 22.10.2015
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Transfer domain to another client
*/
?>
<div class="container">
  <div class="row">
    <div class="span10 offset1">
      <h2><?php echo Yii::t('domain','You are about transfer domain {domain} to another client',array('{domain}'=>$model->name))?></h2>
    </div>
  </div>
  <div class="row">
    <div class="span10 offset1">
      <div class="alert alert-block">
        <strong><?php echo Yii::t('domain','Warning!'); ?></strong> <?php echo Yii::t('domain','This operation is not reversible!'); ?>
      </div>

      <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>get_class($model),
        'type'=>'horizontal',
      ))?>

        <div class="control-group<?php echo $error ? ' error' : '' ?>">
          <label class="control-label"><?php echo Yii::t('domain','Type new client e-mail address'); ?></label>
          <div class="controls">
            <?php echo CHtml::textField('client', $email, array('class'=>'input-block-level')); ?>
            <?php
              switch ($error) {
                case 'empty':
                  $message = Yii::t('domain','You must give e-mail address of the new client.');
                  break;
                case 'not-found':
                  $message = Yii::t('domain','Client with provided e-mail address not found in the database.');
                  break;
                default:
                  $message = false;
              }
              if ($message) {
                echo CHtml::tag('span',array('class'=>'help-block'),$message);
              }
            ?>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-danger"><s class="icon-retweet icon-white"></s> <?php echo Yii::t('domain','Transfer')?></button>
          <a class="btn btn-link" href="<?php echo $returnUrl?>"><?php echo Yii::t('common','Cancel')?></a>
        </div>

        <?php echo CHtml::hiddenField('returnUrl',$returnUrl)?>
      <?php $this->endWidget()?>
    </div>
  </div>
</div>
