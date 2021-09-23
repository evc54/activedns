<?php
return array(
  'isHtml'=>true,
  'subject'=>"{siteName}: domain expiration warning",
  'body'=>'<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{siteName} domain expiration warning</title>
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
          <td colspan="5" align="right" style="padding: 10px;"><font style="font-family: arial; font-size: 11px; color: #aaaaaa;">Domain expiration warning</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 25px 10px 10px;"><font style="font-family: arial; font-size: 24px;">Some of your domains expiring soon</font></td>
        </tr>
        <tr>
          <td colspan="10">
            <table width="580" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="left" style="padding: 3px 10px; border-bottom: 1px solid #dddddd;"><font style="font-family: arial; font-size: 11px; font-weight: bold;">Domain name</font></td>
                <td width="70" align="left" style="padding: 3px 0; border-bottom: 1px solid #dddddd;"><font style="font-family: arial; font-size: 11px; font-weight: bold;">Status</font></td>
                <td width="200" align="left" style="padding: 3px 0; border-bottom: 1px solid #dddddd;"><font style="font-family: arial; font-size: 11px; font-weight: bold;">Expiring at</font></td>
              </tr>
              {expires}
              <tr>
                <td align="left" colspan="4" style="padding: 3px 10px;"><font style="font-family: arial; font-size: 14px;"><a href="{loginUrl}">Sign in into your account</a></font></td>
              </tr>
            </table>
          </td>
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
  'expire'=>'
<tr>
  <td align="left" valign="top" style="padding: 3px 10px; border-bottom: 1px solid #dddddd;"><font style="font-family: arial; font-size: 12px;">{name}</font></td>
  <td align="left" valign="top" style="padding: 3px 0; border-bottom: 1px solid #dddddd;"><font style="font-family: arial; font-size: 12px;">{status}</font></td>
  <td align="left" valign="top" style="padding: 3px 0; border-bottom: 1px solid #dddddd;"><font style="font-family: arial; font-size: 12px;">{date}</font></td>
</tr>',
  'embeddings'=>array(
    'logo'=>dirname(__FILE__) . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'logo.png',
  ),
  'attachments'=>array(
  ),
);
