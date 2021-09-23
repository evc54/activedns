<?php
return array(
  'isHtml'=>true,
  'subject'=>"{siteName}: уведомление об изменении адреса электронной почты",
  'body'=>'<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{siteName} уведомление об изменении адреса электронной почты</title>
  </head>
  <body style="background-color: #f0f0f0;">
    <center>
      <table width="580" border="0" cellspacing="0" cellpadding="0" style="background-color: #f0f0f0;">
        <tr>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
          <td width="58"></td>
        </tr>
        <tr style="background-color: #3A3A3A; ">
          <td colspan="5" align="left" style="padding: 15px 10px 10px 10px;"><font style="font-family: arial; font-size: 11px;"><a href="{siteUrl}"><img src="cid:logo" /></a></font></td>
          <td colspan="5" align="right" style="padding: 10px;"><font style="font-family: arial; font-size: 11px; color: #aaaaaa;">Уведомление об изменении адреса электронной почты</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 25px 10px 10px;"><font style="font-family: arial; font-size: 24px;">Адрес электронной почты был изменен</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 17px;">Вы успешно изменили адрес электронной почты в Вашей учетной записи на сайте {siteName} на новый адрес {newEmail}</font></td>
        </tr>
        <tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 14px;">В случае, если Вы думаете, что это ошибка, Вы можете связаться с системным оператором сайта {siteName} по следующему адресу: <a href="{adminEmail}">{adminEmail}</a></font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 14px;">Если Вы не знаете о чем идет речь, просто игнорируйте письмо</font></td>
        </tr>
        <tr>
          <td colspan="10" style="padding: 5px; border-bottom: 1px solid #aaaaaa;"></td>
        </tr>
        <tr>
          <td align="left" valign="top" colspan="5" style="padding: 5px 10px 15px;"><a href="{siteUrl}" style="text-decoration: none;"><font style="font-family: arial; font-size: 12px; color: black;"><b>{siteName}</b> &ndash; быстрый хостинг DNS</font></a></td>
          <td align="right" valign="top" colspan="5" style="padding: 5px 10px 15px;"><a href="http://viratechnologies.ru/" style="text-decoration: none;"><font style="font-family: arial; font-size: 12px; color: #444444;">Vira Technologies</font></a></td>
        </tr>
      </table>
    </center>
  </body>
</html>',
  'embeddings'=>array(
    'logo'=>dirname(__FILE__) . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'logo.png',
  ),
  'attachments'=>array(
  ),
);
