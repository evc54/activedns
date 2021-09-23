<?php
/**
  Project       : ActiveDNS
  Document      : Controller.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Controller class
*/
class Controller extends CController
{
  public $layout = '//layouts/frontend';
  public $themeUrl;
  public $baseUrl;
  public $cs;

  public $_counters = array();

  public function filters()
  {
    return array(
      'accessControl',
    );
  }

  public function beforeAction($action)
  {
    parent::beforeAction($action);
    self::setUserSpecificData();
    $this->themeUrl = Yii::app()->theme->baseUrl;
    $this->baseUrl = Yii::app()->request->baseUrl;
    $this->cs = Yii::app()->clientScript;
    $this->cs->registerCoreScript('jquery');

    if ($language = Yii::app()->request->getParam('lang')) {
      if (isset(Yii::app()->params['languages'][$language])) {
        $this->setLanguageCookie($language);
        Yii::app()->setLanguage($language);
      }
    }
    
    Yii::app()->format->booleanFormat = array(
      1 => Yii::t('common', 'Yes'),
      0 => Yii::t('common', 'No'),
    );

    $this->pageTitle = $this->getTitle($this->route);

    return true;
  }

  public function cssUrl($css)
  {
    return strpos($css,'.css') !== false ? ($this->themeUrl . '/css/' . $css) : ($this->themeUrl . '/css/' . $css . '.css');
  }

  public function scriptUrl($script)
  {
    $scriptName = $script . (YII_DEBUG ? '' : '.min');
    if (strpos($scriptName,'.js') !== strlen($scriptName)-3) {
      $scriptName .= '.js';
    }
    return $this->baseUrl . '/js/' . $scriptName;
  }

  public function imageUrl($imageFile)
  {
    return $this->themeUrl . '/i/' . $imageFile;
  }

  public function emptyCounter($counter)
  {
    if ($this->_counters === array()) {
      $this->_counters = $this->loadCounters();
    }

    if (isset($this->_counters[$counter]) && $this->_counters[$counter] != 0) {
      return false;
    }

    return true;
  }

  public function getCounter($counter)
  {
    if ($this->_counters === array()) {
      $this->_counters = $this->loadCounters();
    }

    if (isset($this->_counters[$counter])) {
      return $this->_counters[$counter];
    }

    return 0;
  }

  public function loadCounters()
  {
    $model = Yii::app()->user->getModel();

    $counters = array(
      'unseenEventsQty'=>$model->unseenEventsQty(),
    );

    if ($model->role == User::ROLE_ADMIN) {
      $counters['totalDomainsQty'] = Domain::model()->count();
      $counters['totalTemplatesQty'] = Template::model()->count();
      $counters['unseenSupportQty'] = SupportTicket::model()->unseen($model->lastSupportSeen)->count();
      $counters['totalUsersQty'] = User::model()->count();
      $counters['totalNameserversQty'] = NameServer::model()->count();
      $counters['totalPlansQty'] = PricingPlan::model()->count();
      $counters['commonTemplatesQty'] = Template::model()->common()->count();
      $counters['totalNewsQty'] = News::model()->count();
    }
    else {
      $counters['totalDomainsQty'] = $model->totalDomainsQty;
      $counters['totalTemplatesQty'] = $model->totalTemplatesQty;
      $counters['unseenSupportQty'] = $model->unseenSupportQty();
    }

    return $counters;
  }

  public function getNavigationPath()
  {
    $path = '//snippets/menus/user';
    $model = Yii::app()->user->getModel();

    if ($model !== null) {
      switch ($model->role) {
        case User::ROLE_ADMIN:
          $path = '//snippets/menus/admin';
          break;
        default:
      }
    }

    return $path;
  }

  public function registerCommonScripts()
  {
    $this->cs->registerScript('disabledItemBehavior',"$(document).on('click','a.disabled-item',function(e){e.preventDefault();e.stopPropagation();});",CClientScript::POS_READY);
  }

  public function getFirstError($model)
  {
    $error = $model->getErrors();

    while (is_array($error)) {
      $error = current($error);
    }

    return $error;
  }

  public static function setUserSpecificData($user = null)
  {
    $primaryLanguage = Config::get('PrimaryLanguage');
    $cookieLanguage = null;

    if (Yii::app()->hasComponent('user')) {
      $cookieLanguage = Yii::app()->controller->getLanguageCookie();

      if ($user === null) {
        if (!Yii::app()->user->isGuest) {
          $user = Yii::app()->user->getModel();
        }
        elseif (Yii::app()->hasComponent('request')) {
          // set browser' preferred language
          $preferredLanguage = substr(Yii::app()->request->preferredLanguage, 0, 2);
          if (empty($cookieLanguage) && $preferredLanguage != $primaryLanguage && isset(Yii::app()->params['languages'][$preferredLanguage])) {

            Yii::app()->setLanguage($preferredLanguage);
            Yii::app()->controller->setLanguageCookie($preferredLanguage);

            Yii::app()->user->setFlash(
              'revertToSourceLanguage',
              'Application language automatically set to &laquo;' .
                Yii::app()->params['languages'][$preferredLanguage] . '&raquo;. ' .
                CHtml::link('Revert to &laquo;' . Yii::app()->params['languages'][$primaryLanguage] . '&raquo;', Yii::app()->controller->createUrl(Yii::app()->controller->route, CMap::mergeArray(array('lang' => $primaryLanguage), Yii::app()->controller->actionParams)))
            );

            $cookieLanguage = $preferredLanguage;
          }
        }
      }
    }

    if ($user !== null) {
      $dateFormat = $user->dateFormat;
      $timeFormat = $user->timeFormat;
      $language = $user->language;
      $timeZone = $user->timeZone;
    }

    if (empty($dateFormat)) {
      $dateFormat = Config::get('DateFormat');
    }

    if (empty($timeFormat)) {
      $timeFormat = Config::get('TimeFormat');
    }

    if (empty($language)) {
      $language = empty($cookieLanguage) ? $primaryLanguage : $cookieLanguage;
    }

    if (empty($timeZone)) {
      $timeZone = Config::get('TimeZone');
    }

    Yii::app()->format->dateFormat = $dateFormat;
    Yii::app()->format->datetimeFormat = $dateFormat . ' ' . $timeFormat;
    Yii::app()->setLanguage($language);
    if (!empty($timeZone)) {
      date_default_timezone_set($timeZone);
    }
  }

  public function getLanguageCookie($defaultLanguage = null)
  {
    if (isset(Yii::app()->request->cookies['language'])) {
      $cookie = Yii::app()->request->cookies['language'];
      return is_object($cookie) ? $cookie->value : $cookie;
    }

    return $defaultLanguage;
  }

  public function setLanguageCookie($language)
  {
    $languageCookie = new CHttpCookie('language',$language);
    $languageCookie->expire = time() + 31104000; // 1 year
    Yii::app()->request->cookies['language'] = $languageCookie;
  }
  
  public function getTitle($route)
  {
  }
  
  public function titleLabel($label)
  {
    return mb_strtoupper(mb_substr($label, 0, 1, Yii::app()->charset), Yii::app()->charset) . mb_strtolower(mb_substr($label, 1, mb_strlen($label), Yii::app()->charset), Yii::app()->charset);
  }
}
