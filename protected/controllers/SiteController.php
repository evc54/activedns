<?php
/**
  Project       : ActiveDNS
  Document      : controllers/SiteController.php
  Document type : PHP script file
  Created at    : 29.11.2011
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Application frontend controller
*/
class SiteController extends Controller
{
  public function filters()
  {
      return array(
          'accessControl',
      );
  }

  public function accessRules()
  {
    return CMap::mergeArray(
      parent::accessRules(),
      array(
        array(
          'allow',
          'actions' => array(
              'index',
              'pricing',
              'terms',
              'contact',
              'signin',
              'signup',
              'restore',
              'message',
              'captcha',
              'confirm',
              'error',
            ),
            'users' => array('*'),
          ),
          array(
            'allow',
            'actions' => array(
              'signout',
            ),
            'users' => array('@'),
          ),
          array(
            'deny',
            'users' => array('*'),
          ),
      )
    );
  }

  public function actions()
  {
    return array(
      'captcha' => array(
        'class' => 'CCaptchaAction',
        'backColor' => 0xFFFFFF,
      ),
    );
  }

  public function beforeAction($action)
  {
    if (parent::beforeAction($action)) {
      $this->setMetaTags();

      return true;
    }

    return false;
  }

  public function actionIndex()
  {
    $this->render('index');
  }

  public function actionPricing()
  {
    $criteria = new CDbCriteria;
    $criteria->compare('t.status', PricingPlan::STATUS_ENABLED);
    $criteria->order = 't.pricePerYear DESC';
    $model = PricingPlan::model()->findAll($criteria);

    $data = array();
    foreach ($model as $plan) {
      $data[$plan->type][] = $plan;
    }

    $this->render('pricing', array(
      'data'   => $data,
      'types'  => array_reverse(PricingPlan::model()->attributeLabelsType(), true),
      'cycles' => PricingPlan::model()->attributeLabelsBilling(),
    ));
  }

  public function actionError()
  {
    $error = Yii::app()->errorHandler->error;
    if ($error) {
      if (Yii::app()->request->isAjaxRequest) {
        echo $error['message'];
      }
      else {
        if (!Yii::app()->user->isGuest) {
          $this->layout = 'backend';
        }
        $this->render('error', $error);
      }
    }
  }

  public function actionSignup()
  {
    $r = Yii::app()->request;

    if (!Yii::app()->user->isGuest) {
      $this->redirect($this->createUrl('panel/index'));
    }

    if ($r->getParam('Signup')) {
      $model = new Signup('manual');
    }
    else {
      $model = new Signup('create');
      $model->email = $r->getParam('email');
    }

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam('Signup');
      if ($model->validate()) {
        $plan = PricingPlan::model()->findByPk(Config::get('NewAccountPlan'));
        $user = new User('create');
        $user->email = $model->email;
        $user->role = User::ROLE_USER;
        $user->status = User::USER_ENABLED;
        $user->idPricingPlan = $plan->id;
        $user->ns1 = $plan->defaultNameserverMaster;
        $user->ns2 = $plan->defaultNameserverSlave1;
        $user->ns3 = $plan->defaultNameserverSlave2;
        $user->ns4 = $plan->defaultNameserverSlave3;
        $clearPassword = $user->generatePassword(Yii::app()->params['minPasswordLength']);
        $user->newPassword = $clearPassword;
        $user->newPasswordConfirm = $clearPassword;
        if ($user->save()) {
          $credentials = new Credentials;
          $credentials->username = $user->email;
          $credentials->password = $clearPassword;
          $credentials->stayLoggedIn = true;
          $credentials->login();

          $template = Yii::app()->mailer->getTemplate('signupNotify');
          if ($template !== null) {
            $params = array(
              '{siteName}'   => Yii::app()->name,
              '{siteUrl}'    => Yii::app()->params['siteUrl'],
              '{adminEmail}' => Yii::app()->params['adminEmail'],
              '{email}'      => $user->email,
              '{password}'   => $clearPassword,
              '{loginUrl}'   => Yii::app()->params['siteUrlLogin'],
            );
            Yii::app()->mailer->send(
              $user->email,
              $template['subject'],
              $template['body'],
              $params,
              $template['isHtml'],
              $template['attachments'],
              $template['embeddings']
            );
          }

          $this->redirect(array('/panel/index'));
        }
        else {
          Yii::app()->user->setFlash('error', Yii::t('error', 'An error occured while signing up: {error}', array('{error}' => $this->getFirstError($user))));
        }
      }
    }

    $this->render('signup', array(
      'model' => $model,
    ));
  }

  public function actionSignin()
  {
    $r = Yii::app()->request;
    if (!Yii::app()->user->isGuest) {
      $this->redirect($this->createUrl('panel/index'));
    }

    $model = new Credentials;
    $error = false;

    if ($r->getParam(get_class($model))) {
      $model->attributes = $r->getParam(get_class($model));
      Yii::app()->user->setFlash('username', $model->username);
      if ($model->validate() && $model->login()) {
        $this->redirect(Yii::app()->user->getReturnUrl($this->createUrl('panel/index')));
      }
    }

    if ($model->hasErrors()) {
      $error = $this->getFirstError($model);
    }
    $model->password = '';

    $this->render('signin', array(
      'model' => $model,
      'error' => $error,
    ));
  }

  public function actionSignout()
  {
    Yii::app()->user->logout();
    $this->redirect(Yii::app()->homeUrl);
  }

  public function actionMessage()
  {
    $this->render('message');
  }

  public function actionRestore()
  {
    if (!Yii::app()->user->isGuest) {
      $this->redirect($this->createUrl('panel/index'));
    }

    $r = Yii::app()->request;

    if ($r->getParam('_')) {
      $restoreAccessCriteria = new CDbCriteria;
      $restoreAccessCriteria->compare('t.timestamp', $r->getParam('_'));
      $restoreAccessCriteria->compare('t.activeBefore', '>=' . time());
      $restoreAccess = RestoreAccess::model()->find($restoreAccessCriteria);
      if ($restoreAccess === null) {
        throw new CHttpException(403, Yii::t('error', 'Access not allowed'));
      }

      $model = new ChangePassword;
      $model->key = $_GET['_'];
      if ($r->getParam(get_class($model))) {
        $model->attributes = $r->getParam(get_class($model));
        if ($model->validate()) {
          $user = User::model()->findByAttributes(array('email' => $restoreAccess->email));
          if ($user->setPassword($model->newPassword)) {
            $restoreAccess->delete();

            $template = Yii::app()->mailer->getTemplate('passwordNotify');
            if ($template !== null) {
              $params = array(
                '{siteName}'   => Yii::app()->name,
                '{siteUrl}'    => Yii::app()->params['siteUrl'],
                '{adminEmail}' => Yii::app()->params['adminEmail'],
              );
              Yii::app()->mailer->send(
                $user->email,
                $template['subject'],
                $template['body'],
                $params,
                $template['isHtml'],
                $template['attachments'],
                $template['embeddings']
              );
            }

            Yii::app()->user->setFlash('success', Yii::t('success', 'Password successfully changed. Now you can {signin}', array('{signin}' => CHtml::link(Yii::t('common', 'sign in'), $this->createUrl('site/signin')))));
          }
          else {
            Yii::app()->user->setFlash('error', Yii::t('error', 'An error occurred while processing password change'));
          }
          $this->redirect(array('/site/message'));
        }
      }

      $this->render('change', array(
        'model' => $model,
      ));
    }
    else {
      $model = new Restore;
      if ($r->getParam(get_class($model))) {
        $model->attributes = $r->getParam(get_class($model));
        if ($model->validate()) {
          $restoreAccess = new RestoreAccess;
          $restoreAccess->email = $model->email;
          $restoreAccess->save();
          $restoreAccess->refresh();
          $user = User::model()->findByAttributes(array('email' => $model->email));

          $template = Yii::app()->mailer->getTemplate('passwordConfirmation');
          if ($template !== null) {
            $params = array(
              '{siteName}'       => Yii::app()->name,
              '{siteUrl}'        => Yii::app()->params['siteUrl'],
              '{adminEmail}'     => Yii::app()->params['adminEmail'],
              '{confirmUrl}'     => $this->createAbsoluteUrl('/site/restore',array('_' => $restoreAccess->timestamp)),
              '{expireDatetime}' => Yii::app()->format->formatDatetime($restoreAccess->activeBefore),
            );
            Yii::app()->mailer->send(
              $user->email,
              $template['subject'],
              $template['body'],
              $params,
              $template['isHtml'],
              $template['attachments'],
              $template['embeddings']
            );
          }

          Yii::app()->user->setFlash('success', Yii::t('success', 'Please check your e-mail for further instructions'));
          $this->redirect(array('/site/message'));
        }
      }

      $model->email = $r->getParam('username','');
      $this->render('restore', array(
        'model' => $model,
      ));
    }
  }

  public function actionConfirm($_)
  {
    $change = ChangeEmail::model()->find("MD5(CONCAT(t.userID,t.email,t.newEmail,t.activeBefore)) = :_",array(':_' => $_));
    if ($change !== null) {
      if ($change->activeBefore > time()) {
        $model = User::model()->findByPk($change->userID);
        if ($model !== null && strlen($model->email) == strlen($change->email) && strncasecmp($model->email, $change->email, strlen($model->email)) === 0) {
          $model->email = $change->newEmail;
          $model->save();
          $change->delete();

          $template = Yii::app()->mailer->getTemplate('emailNotify');
          if ($template !== null) {
            $params = array(
              '{siteName}'   => Yii::app()->name,
              '{siteUrl}'    => Yii::app()->params['siteUrl'],
              '{newEmail}'   => $model->email,
              '{adminEmail}' => Yii::app()->params['adminEmail'],
            );
            Yii::app()->mailer->send(
              $model->email,
              $template['subject'],
              $template['body'],
              $params,
              $template['isHtml'],
              $template['attachments'],
              $template['embeddings']
            );
          }

          Yii::app()->user->setFlash('success', Yii::t('success', 'Your e-mail has been changed'));
          $this->redirect(array('/site/message'));
        }
      }
    }

    Yii::app()->user->setFlash('error', Yii::t('error', 'Sorry, confirmation link has been expired'));
    $this->redirect(array('/site/message'));
  }
  
  public function actionTerms()
  {
    $this->render('terms');
  }

  public function actionContact()
  {
    $this->render('contact');
  }
  
  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'site/index':
        $title = Yii::t('titles', 'Easy Managed DNS Hosting');
        break;
      case 'site/pricing':
        $title = Yii::t('titles', 'Pricing Plans');
        break;
      case 'site/signin':
        $title = Yii::t('titles', 'Sign Into Dashboard');
        break;
      case 'site/signup':
        $title = Yii::t('titles', 'Sign Up a New Account');
        break;
      case 'site/restore':
        $title = Yii::t('titles', 'Restore Forgotten Password');
        break;
      case 'site/message':
        $title = Yii::t('titles', 'Important Message');
        break;
      case 'site/confirm':
        $title = Yii::t('titles', 'Restore Password Confirmation');
        break;
      case 'site/error':
        $title = Yii::t('titles', 'Error Message');
        break;
      case 'site/terms':
        $title = Yii::t('titles', 'Terms And Conditions');
        break;
      case 'site/contact':
        $title = Yii::t('titles', 'Contacts');
        break;
    }

    if (empty($title)) {
      $title = Yii::app()->name;
    }
    else {
      $title .= ' · ' . Yii::app()->name;
    }

    return $title;
  }

  public function setMetaTags()
  {
    $meta = $this->getMetaTags();
    $this->cs->registerMetaTag($meta['description'], 'description');
    $this->cs->registerMetaTag(implode(',', $meta['keywords']), 'keywords');
  }

  public function getMetaTags()
  {
    return array(
      'keywords' => array(
        Yii::t('meta', 'domain'),
        Yii::t('meta', 'name'),
        Yii::t('meta', 'dns'),
        Yii::t('meta', 'management'),
        Yii::t('meta', 'hosting'),
        Yii::t('meta', 'premium'),
        Yii::t('meta', 'free account'),
        Yii::t('meta', 'high reliable'),
        Yii::t('meta', 'high performance'),
        Yii::t('meta', 'easy managed'),
        Yii::t('meta', 'service api'),
        Yii::t('meta', '99.99% uptime'),
        Yii::t('meta', 'ddos protect'),
        Yii::t('meta', 'dynamic'),
      ),
      'description' => Yii::t('meta', 'Easy Managed DNS Hosting. High reliable service. Very high performance. Low prices.'),
    );
  }
}
