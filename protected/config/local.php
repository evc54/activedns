<?php

// local overrides for activedns.net
return array(
  'name'=>'ACTIVEDNS.NET',
  'components'=>array(
    'mailer'=>array(
      'fromEmail'=>'robot@activedns.net',//Letter from e-mail
      'fromName'=>'ACTIVEDNS.NET',//Letter from name
    ),
  ),
  'params'=>array(
    'siteUrl'=>'http://activedns.net/',
    'siteUrlLogin'=>'http://activedns.net/signin/',
    'siteUrlRestore'=>'http://activedns.net/restore/',
    'adminEmail'=>'devops@viratechnologies.ru',
    'contactEmail'=>'devops@viratechnologies.ru',
    'paymentGateway'=>array(
      'login'=>'activedns', // merchant login
      'password1'=>'123456', // merchant password #1
      'password2'=>'654321', // merchant password #2
      'url'=>'https://auth.robokassa.ru/Merchant/Index.aspx', // gateway url
      'currenciesMapping'=>array(
        'USD'=>array(
          'index'=>'WMZM',
          'rate'=>1,
        ),
        'EUR'=>array(
          'index'=>'WMEM',
          'rate'=>1,
        ),
        'RUB'=>array(
          'index'=>'WMRM',
          'rate'=>1,
        ),
        'CNY'=>array(
          'index'=>'WMZM',
          'rate'=>0.161447,
        ),
      ),
    ),
  ),
);
