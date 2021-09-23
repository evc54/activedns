<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/datepicker.php
  Document type : PHP script file
  Created at    : 26.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Datepicker snippet
*/

$this->cs->registerScriptFile($this->scriptUrl('bootstrap-datepicker'));
$this->cs->registerScriptFile($this->scriptUrl('bootstrap-datepicker-config-' . Yii::app()->getLanguage()));
