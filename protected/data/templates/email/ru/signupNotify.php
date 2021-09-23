<?php
return array(
  'isHtml'=>true,
  'subject'=>"{siteName}: уведомление о регистрации учетной записи",
  'body'=>'<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{siteName} уведомление о регистрации учетной записи</title>
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
          <td colspan="5" align="right" style="padding: 10px;"><font style="font-family: arial; font-size: 11px; color: #aaaaaa;">Информация о Вашей учетной записи</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 25px 10px 10px;"><font style="font-family: arial; font-size: 24px;">Благодарим Вас за регистрацию!</font></td>
        </tr>
        <tr>
          <td colspan="10" align="left" style="padding: 10px;"><font style="font-family: arial; font-size: 17px;">Информация о Вашей учетной записи на сайте {siteName}</font></td>
        </tr>
        <tr>
          <td colspan="10" style="padding: 5px; border-top: 1px solid #aaaaaa;"></td>
        </tr>
        <tr>
          <td colspan="10">
            <table width="580" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="210" align="left" style="padding: 3px 10px;"><font style="font-family: arial; font-size: 14px;">Адрес электронной почты:</font></td>
                <td align="left"><font style="font-family: arial; font-size: 14px; font-weight: bold;">{email}</font></td>
              </tr>
              <tr>
                <td width="210" align="left" valign="top" style="padding: 3px 10px;"><font style="font-family: arial; font-size: 14px;">Пароль:</font></td>
                <td align="left" valign="top" style="padding:3px 0;"><font style="font-family: arial; font-size: 14px; font-weight: bold; border: 1px solid #d0d0d0; padding: 5px; color: #f0f0f0;">{password}</font><br /><font style="font-family: arial; font-size: 11px;">пароль скрыт в целях безопасности, чтобы посмотреть пароль выполните двойное нажатие левой клавиши мыши на сером поле</font></td>
              </tr>
              <tr>
                <td align="left" colspan="4" style="padding: 3px 10px;"><font style="font-family: arial; font-size: 14px;"><a href="{loginUrl}">Войдите на сайт</a></font></td>
              </tr>
            </table>
          </td>
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
