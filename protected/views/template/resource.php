<?php
/**
  Project       : ActiveDNS
  Document      : views/template/rr/resource.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Template records update view
*/

foreach ($this->getRRTypes() as $type) {
  $this->renderPartial('/domain/rr/grid',array(
    'model'=>$model,
    'rr'=>TemplateRecord::model()->search($model->id,$type),
    'type'=>$type,
  ));
}
