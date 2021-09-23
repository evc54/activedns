<?php
/**
  Project       : ActiveDNS
  Document      : views/plan/detail.php
  Document type : PHP script file
  Created at    : 05.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Pricing plan's detail view
*/

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data'=>$model,
  'attributes'=>array(
    array('name'=>'id'),
    array('name'=>'status','value'=>$model->getAttributeLabelStatus()),
    array('name'=>'type','value'=>$model->getAttributeLabelType()),
    array('name'=>'title'),
    array('name'=>'domainsQty','value'=>$model->domainsQty > 0 ? $model->domainsQty : '&infin;','type'=>'html'),
    array('name'=>'usersQty','value'=>$model->usersQty > 0 ? $model->usersQty : '&infin;','type'=>'html'),
    array('name'=>'nameserversQty'),
    array('name'=>'minTtl'),
    array('name'=>'accessApi','type'=>'boolean'),
    array('name'=>'pricePerYear','value'=>$model->pricePerYear > 0 ? '$' . $model->pricePerYear : '&mdash;','type'=>'html'),
    array('name'=>'pricePerMonth','value'=>$model->pricePerMonth > 0 ? '$' . $model->pricePerMonth : '&mdash;','type'=>'html'),
    array('name'=>'billing','value'=>$model->getAttributeLabelBilling()),
  ),
));
