<?php
/**
  Project       : ActiveDNS
  Document      : views/layouts/backend.php
  Document type : PHP script file
  Created at    : 04.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Backend layout
*/

$this->beginContent('//layouts/main');
$this->renderPartial($this->getNavigationPath());
echo $content;
$this->renderPartial('//snippets/footer');
$this->endContent();
