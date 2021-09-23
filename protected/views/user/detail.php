<?php
/**
  Project       : ActiveDNS
  Document      : views/user/detail.php
  Document type : PHP script file
  Created at    : 16.09.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : User's detail view
*/

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data'=>$model,
  'attributes'=>array(
    array('name'=>'id'),
    array('name'=>'status'),
    array('name'=>'email'),
    array('name'=>'realname'),
    array('name'=>'idPricingPlan','value'=>$model->plan ? $model->plan->title : ''),
    array('name'=>'paidTill'),
    array('name'=>'role'),
    array('name'=>'totalDomainsQty','label'=>Yii::t('user','Total Domains Hosted')),
    array('name'=>'activeDomainsQty','label'=>Yii::t('user','Active Domains')),
  ),
));
