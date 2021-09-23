<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/selectors/zone.php
  Document type : PHP script file
  Created at    : 06.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Zone version selector
*/
$this->widget('bootstrap.widgets.TbMenu',array(
  'id'=>'zone-selector',
  'type'=>'list',
  'items'=>$this->getZoneMenu($model,$ajax,$zone->id),
));
