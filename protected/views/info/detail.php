<?php
/**
  Project       : ActiveDNS
  Document      : views/info/detail.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News's management detail partial view
*/

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data'=>$model,
  'attributes'=>array(
    array('name'=>'id'),
    array('name'=>'public','type'=>'boolean'),
    array('name'=>'create','type'=>'datetime'),
    array('name'=>'publish','type'=>'datetime'),
    array('name'=>'update','type'=>'datetime'),
    array('name'=>'author.realname','label'=>Yii::t('news','Author')),
    array('name'=>'currentLanguageContent.title'),
    array('name'=>'currentLanguageContent.announce'),
    array('name'=>'currentLanguageContent.fulltext'),
  ),
));
