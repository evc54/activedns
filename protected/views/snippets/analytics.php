<?php
/**
  Project       : ActiveDNS
  Document      : views/snippets/analytics.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Google analytics snippet
*/

echo Config::get('GoogleAnalyticsCode');
echo Config::get('CustomCounterCode');
