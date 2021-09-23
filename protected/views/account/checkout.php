<?php
/**
  Project       : ActiveDNS
  Document      : views/account/checkout.php
  Document type : PHP script file
  Created at    : 06.04.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Redirect to payment gateway
*/?>
<html>
  <body>
    <form method="POST" action="<?= $gatewayUrl ?>" id="deposit">
      <input type="hidden" name="MrchLogin" value="<?= $gatewayLogin ?>" />
      <input type="hidden" name="OutSum" value="<?= $amount ?>" />
      <input type="hidden" name="InvId" value="<?= $invoice ?>" />
      <input type="hidden" name="Desc" value="<?= $description ?>" />
      <input type="hidden" name="SignatureValue" value="<?= $signature ?>" />
      <input type="hidden" name="IncCurrLabel" value="<?= $currency ?>" />
      <input type="hidden" name="Email" value="<?= $email ?>" />
      <input type="hidden" name="Culture" value="<?= $language ?>" />
      <input type="submit" name="Continue" value="Continue" />
    </form>
    <script language="JavaScript">
      setTimeout(function(){document.getElementById('deposit').submit();},0);
    </script>
  </body>
</html>
