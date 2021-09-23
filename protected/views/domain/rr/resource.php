<?php
/**
  Project       : ActiveDNS
  Document      : views/domain/rr/resource.php
  Document type : PHP script file
  Created at    : 01.07.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Resource records update view
*/
foreach ($this->getRRTypes() as $type) {
  $this->renderPartial('rr/grid',array(
    'model'=>$model,
    'rr'=>ResourceRecord::model()->search($zone->id,$type),
    'type'=>$type,
  ));
}
