<?php
/**
  Project       : ActiveDNS
  Document      : views/nameserver/detail.php
  Document type : PHP script file
  Created at    : 20.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameserver's management detail partial view
*/

$this->widget('bootstrap.widgets.TbDetailView', array(
  'data'=>$model,
  'attributes'=>array(
        array('name'=>'id'),
        array('name'=>'name'),
        array('name'=>'load'),
  ),
));
