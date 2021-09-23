<?php
/**
  Project       : ActiveDNS
  Document      : views/site/message.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Common message page view
*/
?>
<div class="container">
</div>
<?php $this->widget('bootstrap.widgets.TbAlert',array(
  'htmlOptions'=>array(
    'style'=>'position: fixed; left: 50%; top: 50%; margin-left: -350px; margin-top: -40px; width: 700px;',
    'class'=>'fade in',
  ),
))?>
