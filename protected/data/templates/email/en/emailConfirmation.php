<?php
return array(
  'isHtml'=>true,
  'subject'=>"{siteName}: e-mail address change confirmation",
  'body'=>'<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{siteName} e-mail change letter</title>
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
          <td colspan="5" align="right" style="padding: 10px;"><font style="font-family: arial; font-size: 11px; color: #aaaaaa;">Change e-mail</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 25px 10px 10px;"><font style="font-family: arial; font-size: 24px;">New e-mail address confirmation</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 17px;">Somebody changed e-mail address at your {siteName} account to new address <b>{newEmail}</b></font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 14px;">To confirm new e-mail address please follow next link or copy it to your browser\'s address bar</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 5px 10px;"><font style="font-family: arial; font-size: 14px;"><a href="{confirmUrl}">{confirmUrl}</a></font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 14px;">This link can be used only once and it\'s expiring at {expireDatetime}</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 14px;">You may want to contact {siteName} system operator <a href="{adminEmail}">{adminEmail}</a> in case you think that is mistake</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 14px;">If you don\'t know what is it about just ignore this letter</font></td>
        </tr>
        <tr>
          <td colspan="10" style="padding: 5px; border-bottom: 1px solid #aaaaaa;"></td>
        </tr>
        <tr>
          <td align="left" valign="top" colspan="5" style="padding: 5px 10px 15px;"><a href="{siteUrl}" style="text-decoration: none;"><font style="font-family: arial; font-size: 12px; color: black;"><b>{siteName}</b> &ndash; fastest DNS hosting</font></a></td>
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
