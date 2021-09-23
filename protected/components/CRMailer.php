<?php
/**
  Project       : ActiveDNS
  Document      : CRMailer.php
  Document type : PHP script file
  Created at    : 27.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Mailer component
*/
class CRMailer extends CApplicationComponent
{
  /**
   * Default encoding
   */
  const DEFAULT_ENCODING = 'base64';
  /**
   * @var string mailer type
   */
  public $mailer = 'mail'; // mail, sendmail or smtp as describes in PHPMailer
  /**
   * @var string message charset
   */
  public $charset;
  /**
   * @var string body encoding
   */
  public $encoding;
  /**
   * @var string sender e-mail
   */
  public $fromEmail;
  /**
   * @var string sender name
   */
  public $fromName;
  /**
   * @var string SMTP host name or IP address
   */
  public $smtpHost;
  /**
   * @var string SMTP port
   */
  public $smtpPort;
  /**
   * @var boolean need authenticate
   */
  public $smtpAuth;
  /**
   * @var string need secure connection - ssl or tls
   */
  public $smtpSecure;
  /**
   * @var string username to authenticate
   */
  public $smtpUsername;
  /**
   * @var string password to authenticate
   */
  public $smtpPassword;
  /**
   * @var boolean SMTP debug mode
   */
  public $smtpDebug = false;

  /**
   * @var array receivers list
   */
  public $receivers = array();

  /**
   * @var string mail subject
   */
  public $subject = '';

  /**
   * @var string mail body
   */
  public $body = '';

  /**
   * @var string alternate mail body if body is html
   */
  public $altBody = '';

  /**
   * @var array Blind cross copy list
   */
  public $bcc = array();

  /**
   * @var integer mail sent status
   */
  private $status = 0;

  /**
   * @var PHPMailer PHPMailer object
   */
  private $mail;

  /**
   * Initialize CRMailer
   */
  public function initialize()
  {
    $this->receivers = array();
    $this->subject = '';
    $this->body = '';
    $this->altBody = '';
    $this->status = 0;
    $this->mail = new PHPMailer\PHPMailer\PHPMailer();
  }

  /**
   * Simple mail sender, returns letter sent status
   * @param mixed $receivers array or string contains receivers email addresses
   * @param string $subject email subject
   * @param string $body email body
   * @param mixed $params params
   * @param boolean $isHtml is HTML content
   * @return mixed
   */
  public function send($receivers, $subject, $body, $params = array(), $isHtml = false, $attach = array(), $embed = array())
  {
    $this->initialize();
    $this->receivers = is_array($receivers) ? $receivers : array($receivers);
    $this->subject = $this->subst($subject, $params);
    $this->body = $this->subst($body, $params);
    if ($isHtml) {
      $this->altBody = strip_tags($this->body);
    }

    $this->mail->IsHTML($isHtml);
    $this->mail->SetFrom($this->fromEmail,$this->fromName);

    $this->mail->Mailer = $this->mailer;
    $this->mail->CharSet = $this->charset ? $this->charset : Yii::app()->charset;
    $this->mail->Encoding = $this->encoding ? $this->encoding : 'base64';
    $this->mail->Subject = $this->subject;
    $this->mail->Body = $this->body;
    $this->mail->AltBody = $this->altBody;

    if ($this->mailer == 'smtp') {
      $this->mail->IsSMTP();
      $this->mail->Host = $this->smtpHost;
      $this->mail->Port = $this->smtpPort;
      $this->mail->SMTPAuth = $this->smtpAuth;
      $this->mail->SMTPSecure = $this->smtpSecure;
      $this->mail->Username = $this->smtpUsername;
      $this->mail->Password = $this->smtpPassword;
      $this->mail->SMTPDebug = $this->smtpDebug;
    }

    foreach ($this->receivers as $receiver) {
      $this->mail->AddAddress($receiver);
    }

    if (is_array($this->bcc) && count($this->bcc)) {
      foreach ($this->bcc as $address) {
        $this->mail->AddBCC($address);
      }
    }

    foreach ($attach as $name => $path) {
      if (file_exists($path) && is_file($path) && is_readable($path)) {
        $this->mail->AddAttachment($path, $name);
      }
    }

    foreach ($embed as $name => $path) {
      if (file_exists($path) && is_file($path) && is_readable($path)) {
        $this->mail->AddEmbeddedImage($path, $name);
      }
    }

    $this->onBeforeSend(new CEvent($this));

    $this->status = $this->mail->Send();

    $this->onAfterSend(new CEvent($this));

    return $this->status;
  }

  /**
   * Substitute parameter values on placeholders and return result message
   * @param string $message message
   * @param mixed $params array of placeholder => value
   * @return string
   */
  public function subst($message,$params = array())
  {
    if (!is_array($params) || !count($params)) {
      return $message;
    }

    foreach ($params as $placeholder => $value) {
      $message = mb_ereg_replace($placeholder, $value, $message);
    }

    return $message;
  }

  /**
   * Fires before sending mail
   * @param CEvent $event event
   */
  public function onBeforeSend($event)
  {
    $this->raiseEvent('onBeforeSend', $event);
  }

  /**
   * Fires after mail was sent
   * @param CEvent $event event
   */
  public function onAfterSend($event)
  {
    $this->raiseEvent('onAfterSend', $event);
  }

  /**
   * Return object property value if exists null otherwise
   * @param string $name property name
   * @return mixed
   */
  public function getProperty($name)
  {
    if (property_exists($this, $name)) {
      return $this->$name;
    }

    return null;
  }

  /**
   * Set object property value if exists
   * @param string $name property name
   * @param mixed $value property value
   */
  public function setProperty($name,$value)
  {
    if (property_exists($this,$name)) {
      $this->$name = $value;
    }
  }

  /**
   * Set object property value if exists, return old value
   * @param string $name property name
   * @param mixed $value property value
   * @return mixed old value
   */
  public function getSetProperty($name,$value)
  {
    if (property_exists($this,$name)) {
      $return = $this->$name;
      $this->$name = $value;
      return $return;
    }

    return null;
  }

  /**
   * Returns email template data or null if it's isn't found
   * @param string $id ID of template
   * @return array Subject, body and attachments array
   */
  public function getTemplate($id)
  {
    if (strpos($id,'.php') !== (strlen($id) - 4)) {
      $id .= '.php';
    }

    $search = array();

    if (!empty(Yii::app()->Controller)) {
      if ($module = Yii::app()->Controller->getModule()) {
        $path = Yii::getPathOfAlias('application.modules.' . $module->id . '.data.templates.email') . DIRECTORY_SEPARATOR;
        $search[] = $path . Yii::app()->language . DIRECTORY_SEPARATOR . $id;
        $search[] = $path . Yii::app()->sourceLanguage . DIRECTORY_SEPARATOR . $id;
      }
    }

    $path = Yii::getPathOfAlias('application.data.templates.email') . DIRECTORY_SEPARATOR;
    $search[] = $path . Yii::app()->language . DIRECTORY_SEPARATOR . $id;
    $search[] = $path . Yii::app()->sourceLanguage . DIRECTORY_SEPARATOR . $id;

    foreach ($search as $file) {
      if (file_exists($file)) {
        $template = require($file);
        break;
      }
    }

    return isset($template) ? $template : null;
  }

  public function setBCC($bcc)
  {
    $this->bcc = is_array($bcc) ? $bcc : array($bcc);
  }
}
