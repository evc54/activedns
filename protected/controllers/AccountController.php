<?php
/**
  Project       : ActiveDNS
  Document      : controllers/AccountController.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Account profile management controller
*/
class AccountController extends Controller
{
  public $layout = '//layouts/backend';
  public $model;

  public function accessRules()
  {
    return CMap::mergeArray(
      parent::accessRules(),
      array(
        array(
          'allow',
          'actions' => array(
            'index',
            'profile',
            'nameserver',
            'alias',
            'unalias',
            'upgrade',
            'renew',
            'checkout',
            'success',
            'fail',
            'remove',
          ),
          'users' => array('@'),
        ),
        array(
          'allow',
          'actions' => array(
            'result',
          ),
          'users' => array('*'),
        ),
        array(
          'deny',
          'users' => array('*'),
        ),
      )
    );
  }

  public function beforeAction($action)
  {
    if (in_array($action->id, array('result'))) {
      return true;
    }

    if (parent::beforeAction($action)) {
      $this->model = Yii::app()->user->getModel();
      $this->model->setScenario('manual');

      return true;
    }

    return false;
  }

  public function actionIndex()
  {
    $this->render('index',array(
      'model' => $this->model,
    ));
  }

  public function actionProfile()
  {
    $r = Yii::app()->request;
    $emailError = '';
    $email = '';
    $soaHostmaster = explode('.', rtrim($this->model->soaHostmaster,' .'));
    $this->model->soaHostmaster = array_shift($soaHostmaster) . '@' . implode('.',$soaHostmaster);

    if ($r->isPostRequest) {
      $success = array(Yii::t('success', 'Account profile updated'));
      $error = array();
      $attributes = $r->getParam(get_class($this->model));
      $email = $r->getParam('email');

      if (isset($attributes['newPassword']) && $attributes['newPassword']) {
        $this->model->setScenario('passwordUpdate');
        $success[] = Yii::t('success','Password changed');
      }

      foreach (array(
        'language',
        'dateFormat',
        'timeFormat',
        'statisticTimeFormat',
        'timeZone',
        'newPassword',
        'newPasswordConfirm',
        'soaHostmaster',
        'soaMinimum',
        'expireNotify',
        'alertNotify',
      ) as $field) {
        if (isset($attributes[$field])) {
          $this->model->setAttribute($field, $attributes[$field]);
        }
      }

      foreach (array(
        'soaRefresh',
        'soaRetry',
        'soaExpire',
      ) as $attribute) {
        $this->model->setAttribute($attribute, $r->getParam($attribute) * $r->getParam($attribute . 'Multiplier'));
      }

      if ($this->model->save()) {
        Yii::app()->cache->delete($this->model->getStatsKey());
        if ($this->model->getScenario() == 'passwordUpdate') {

          $template = Yii::app()->mailer->getTemplate('passwordNotify');
          if ($template !== null) {
            $params = array(
              '{siteName}'=>Yii::app()->name,
              '{siteUrl}'=>Yii::app()->params['siteUrl'],
              '{adminEmail}'=>Yii::app()->params['adminEmail'],
            );
            Yii::app()->mailer->send(
              $this->model->email,
              $template['subject'],
              $template['body'],
              $params,$template['isHtml'],
              $template['attachments'],
              $template['embeddings']
            );
          }
        }
        if ($email) {
          $criteria = new CDbCriteria;
          $criteria->compare('email',$email);
          if (!User::model()->count($criteria)) {
            ChangeEmail::model()->deleteAllByAttributes(array('userID' => $this->model->id));
            $change = new ChangeEmail;
            $change->userID = $this->model->id;
            $change->email = $this->model->email;
            $change->newEmail = $email;
            if ($change->save()) {
              $_ = md5($change->userID . $change->email . $change->newEmail . $change->activeBefore);

              $template = Yii::app()->mailer->getTemplate('emailConfirmation');
              if ($template !== null) {
                $params = array(
                  '{siteName}'       => Yii::app()->name,
                  '{siteUrl}'        => Yii::app()->params['siteUrl'],
                  '{adminEmail}'     => Yii::app()->params['adminEmail'],
                  '{newEmail}'       => $change->newEmail,
                  '{confirmUrl}'     => $this->createAbsoluteUrl('/site/confirm',array('_'=>$_)),
                  '{expireDatetime}' => Yii::app()->format->formatDatetime($change->activeBefore),
                );
                Yii::app()->mailer->send(
                  $this->model->email,
                  $template['subject'],
                  $template['body'],
                  $params,$template['isHtml'],
                  $template['attachments'],
                  $template['embeddings']
                );
              }

              $success[] = Yii::t('success', 'New e-mail confirmation link sent to {mailbox}', array('{mailbox}' => $this->model->email));
            }
            else {
              $error[] = Yii::t('errors', $this->getFirstError($change));
            }
          }
          else {
            $emailError = Yii::t('error', 'This e-mail already registered');
            $error[] = $emailError;
          }
        }
        if ($error !== array()) {
          Yii::app()->user->setFlash('error', implode('. ', $error));
        }
        else {
          Yii::app()->user->setFlash('success', implode('. ', $success));
          $this->redirect(array('profile'));
        }
      }
    }

    $this->render('profile', array(
      'model'      => $this->model,
      'email'      => $email,
      'emailError' => $emailError,
    ));
  }

  public function actionNameserver()
  {
    $r = Yii::app()->request;
    $user = Yii::app()->user->getModel();
    $nameserverNames = CHtml::listData(NameServer::model()->findAll(), 'id', 'name');
    $nameserversAddresses = CHtml::listData(NameServer::model()->findAll(), 'id', 'publicAddress');

    $model = new NameServerAlias('search');
    $model->filterByUser();
    $model->setAttributes($r->getParam(get_class($model), array()));

    if ($r->isAjaxRequest) {
      if ($r->isPostRequest) {
        $json = array();
        $aliasNS = $r->getParam('aliasNS', array());
        $error = $this->validateAliasForm($user->assignedNameserverQty(), $aliasNS);

        if (!empty($error)) {
          $json['error'] = true;
          $json['form'] = $this->renderPartial('alias/form', array(
            'error'=>$error,
            'user'=>$user,
            'nameserversNames'=>$nameserverNames,
            'nameserversAddresses'=>$nameserversAddresses,
            'aliasNS'=>$aliasNS,
            'aliasSource'=>array(
              1=>$user->ns1,
              2=>$user->ns2,
              3=>$user->ns3,
              4=>$user->ns4,
            ),
          ), true);
        }
        else {
          $json['success'] = true;
          $alias = new NameServerAlias;
          $alias->setAttributes(array(
            'idUser'                => $user->id,
            'idNameServerMaster'    => $user->ns1,
            'idNameServerSlave1'    => $user->ns2,
            'idNameServerSlave2'    => $user->ns3,
            'idNameServerSlave3'    => $user->ns4,
            'NameServerMasterAlias' => $aliasNS[1],
            'NameServerSlave1Alias' => $aliasNS[2],
            'NameServerSlave2Alias' => empty($aliasNS[3]) ? '' : $aliasNS[3],
            'NameServerSlave3Alias' => empty($aliasNS[4]) ? '' : $aliasNS[4],
          ));
          $alias->save();
        }

        echo CJSON::encode($json);
      }
      else {
        $this->renderPartial('alias/grid', array(
          'model' => $model,
        ));
      }
      Yii::app()->end();
    }

    $this->render('nameserver', array(
      'model'                => $model,
      'user'                 => $user,
      'nameserversNames'     => $nameserverNames,
      'nameserversAddresses' => $nameserversAddresses,
    ));
  }

  public function actionAlias($id)
  {
    $r = Yii::app()->request;
    $user = Yii::app()->user->getModel();
    $nameserverNames = CHtml::listData(NameServer::model()->findAll(), 'id', 'name');
    $nameserversAddresses = CHtml::listData(NameServer::model()->findAll(),'id', 'publicAddress');
    $error = array();

    $model = NameServerAlias::model()->findByPk($id);
    if ($model === null || $model->idUser != $user->id) {
      throw new CHttpException(404,Yii::t('error', 'Nameserver alias is not found.'));
    }

    $aliasNS = $r->getParam('aliasNS', array(
      1=>$model->NameServerMasterAlias,
      2=>$model->NameServerSlave1Alias,
      3=>$model->NameServerSlave2Alias,
      4=>$model->NameServerSlave3Alias,
    ));

    if ($r->isAjaxRequest) {
      $json = array();

      if ($r->isPostRequest) {
        $error = $this->validateAliasForm($user->assignedNameserverQty(), $aliasNS);
      }

      if ($r->isPostRequest && empty($error)) {
        $model->NameServerMasterAlias = $aliasNS[1];
        $model->NameServerSlave1Alias = $aliasNS[2];
        $model->NameServerSlave2Alias = empty($aliasNS[3]) ? '' : $aliasNS[3];
        $model->NameServerSlave3Alias = empty($aliasNS[4]) ? '' : $aliasNS[4];
        $model->save();
        $json['success'] = true;
      }
      else {
        $json['form'] = $this->renderPartial('alias/form', array(
          'error'                => $error,
          'user'                 => $user,
          'nameserversNames'     => $nameserverNames,
          'nameserversAddresses' => $nameserversAddresses,
          'aliasNS'              => $aliasNS,
          'aliasSource'          => array(
            1 => $model->idNameServerMaster,
            2 => $model->idNameServerSlave1,
            3 => $model->idNameServerSlave2,
            4 => $model->idNameServerSlave3,
          ),
          'usage' => $model->load,
        ), true);
      }

      echo CJSON::encode($json);
      Yii::app()->end();
    }

    $this->render('alias');
  }

  private function validateAliasForm($nsQty, $aliasNS)
  {
    $error = array();

    for ($i = 1; $i <= $nsQty; $i++) {
      if (empty($aliasNS[$i])) {
        $error['aliasNS'][$i] = Yii::t('error', 'Nameserver alias field can not be empty');
      }
      elseif (!preg_match('/([A-Za-z0-9-]+\.)+[A-Za-z0-9-]+\.[A-Za-z0-9-]{2,22}/', $aliasNS[$i])) {
        $error['aliasNS'][$i] = Yii::t('error', 'Alias must be in "subdomain.domain.tld" format');
      }
      else {
        $check = $aliasNS;
        unset($check[$i]);
        if (in_array($aliasNS[$i],$check)) {
          $error['aliasNS'][$i] = Yii::t('error', "Nameserver's aliases must differ");
        }
      }
    }

    return $error;
  }

  public function actionUnalias()
  {
    $r = Yii::app()->request;

    if ($r->isAjaxRequest) {
      $aliases = $r->getParam('aliases', array());
      foreach ($aliases as $id) {
        $c = 0;
        $model = NameServerAlias::model()->findByPk($id);
        if ($model == null || $model->idUser != Yii::app()->user->id) {
          continue;
        }
        if ($model->delete()) {
          $c++;
        }
      }
      echo CJSON::encode(array(
        'success' => Yii::t('nameserver', 'Aliases removed'),
        'message' => Yii::t('nameserver', 'Succesfully removed {n} alias|Succesfully removed {n} aliases', array($c)),
      ));
      Yii::app()->end();
    }

    $this->redirect($this->createUrl('nameserver'));
  }

  public function actionUpgrade()
  {
    $plan = Yii::app()->request->getParam('plan');
    $cycle = Yii::app()->request->getParam('billing', PricingPlan::BILLING_ANNUALLY);
    if (Yii::app()->request->isAjaxRequest) {
      $plan = PricingPlan::model()->findByPk($plan);
      if ($plan != null && $plan->id != Config::get('NewAccountPlan') && $plan->status == PricingPlan::STATUS_ENABLED) {
        echo CJSON::encode($this->calculator($plan, $cycle));
      }
      Yii::app()->end();
    }

    if (Yii::app()->request->isPostRequest) {
      $plan = PricingPlan::model()->findByPk($plan);
      if ($plan != null && $plan->id != Config::get('NewAccountPlan') && $plan->status == PricingPlan::STATUS_ENABLED) {
        $params = $this->calculator($plan, $cycle);
        Yii::app()->user->setState('upgrade', $params);
        $this->render('confirm', array(
          'model'  => $this->model,
          'plan'   => $plan,
          'cycle'  => $cycle,
          'params' => $params,
          'type'   => 'upgrade',
        ));
        Yii::app()->end();
      }
      else {
        Yii::app()->user->setFlash('error', Yii::t('error', 'Selected pricing plan is not valid'));
        $this->redirect(array('upgrade'));
      }
    }

    $criteria = new CDbCriteria;
    $exclude = array(Config::get('NewAccountPlan'));
    $criteria->addNotInCondition('t.id', $exclude);
    $criteria->compare('t.status', PricingPlan::STATUS_ENABLED);
    $criteria->order = 't.pricePerYear DESC';
    $plans = PricingPlan::model()->findAll($criteria);

    $this->render('upgrade', array(
      'model' => $this->model,
      'plans' => $plans,
    ));
  }

  public function actionRenew()
  {
    $renewDate = strtotime($this->model->paidTill);
    switch ($this->model->billing) {
      case PricingPlan::BILLING_ANNUALLY:
        $renewDate += 365 * 24 * 3600;
        $charge = $this->model->plan->pricePerYear;
        break;
      default:
        $renewDate += 30.5 * 24 * 3600;
        $charge = $this->model->plan->pricePerMonth;
    }

    if (Yii::app()->request->isPostRequest) {
      $params = array(
        'plan'    => $this->model->plan->id,
        'cycle'   => $this->model->billing,
        'billing' => $this->model->billing,
        'charge'  => CurrencyHelper::render($charge),
        'time'    => $renewDate,
        'date'    => Yii::app()->format->formatDate($renewDate),
        'amount'  => $charge,
      );

      Yii::app()->user->setState('renew',$params);
      $this->render('confirm', array(
        'model'  => $this->model,
        'plan'   => $this->model->plan,
        'cycle'  => $this->model->billing,
        'params' => $params,
        'type'   => 'renew',
      ));
      Yii::app()->end();
    }

    $this->render('renew', array(
      'model'     => $this->model,
      'renewDate' => $renewDate,
      'charge'    => $charge,
    ));
  }

  public function actionRemove()
  {
    $r = Yii::app()->request;
    $removal = new RemoveAccount;

    if ($r->isPostRequest) {
      $removal->attributes = $r->getParam(get_class($removal));

      if ($removal->validate()) {

        $template = Yii::app()->mailer->getTemplate('removalNotify');
        if ($template !== null) {
          $params = array(
            '{siteName}'   => Yii::app()->name,
            '{siteUrl}'    => Yii::app()->params['siteUrl'],
            '{adminEmail}' => Yii::app()->params['adminEmail'],
          );
          Yii::app()->mailer->send(
            $this->model->email,
            $template['subject'],
            $template['body'],
            $params,
            $template['isHtml'],
            $template['attachments'],
            $template['embeddings']
          );
        }

        Yii::app()->user->logout();
        $this->model->delete();
        Yii::app()->session->open();
        Yii::app()->user->setFlash('success','Your account has been removed. Thank you for using our service.');
        $this->redirect(array('/site/message'));
      }
    }

    $this->render('remove', array(
      'model'   => $this->model,
      'removal' => $removal,
    ));
  }

  public function actionCheckout()
  {
    if (Yii::app()->user->hasState('upgrade')) {
      $type = 'upgrade';
      $params = Yii::app()->user->getState('upgrade');
    }

    if (Yii::app()->user->hasState('renew')) {
      $type = 'renew';
      $params = Yii::app()->user->getState('renew');
    }

    if (empty($params)) {
      $this->redirect(array('index'));
    }

    $plan = PricingPlan::model()->findByPk(intval($params['plan']));
    if ($plan == null) {
      throw new CHttpException(400, Yii::t('error', 'Invalid usage detected. Your activity has been logged for futher investigation.'));
    }

    $primaryCurrency = Config::get('PrimaryCurrency');
    $currency = Yii::app()->params['paymentGateway']['currenciesMapping'][$primaryCurrency]['index'];
    $rate = Yii::app()->params['paymentGateway']['currenciesMapping'][$primaryCurrency]['rate'];

    $invoice = new Invoice;
    $invoice->userID = $this->model->id;
    $invoice->email = $this->model->email;
    $invoice->currency = $currency;
    $invoice->amount = $params['amount'] * $rate;
    $invoice->status = Invoice::STATUS_WAITING;
    $invoice->planID = $plan->id;
    $invoice->billing = $params['cycle'];
    $invoice->paidTill = date('Y-m-d',$params['time']);
    $invoice->save();
    $invoice->invoiceID = Config::get('InvoicePrefix') . $invoice->id;
    $invoice->signature = md5(
      Yii::app()->params['paymentGateway']['login']
      . ':' . $invoice->amount
      . ':' . $invoice->id
      . ':' . Yii::app()->params['paymentGateway']['password1']
    );
    $invoice->save();

    switch ($type) {
      case 'renew':
        $description = Yii::t('account', "{siteName} account '{email}' renew for plan '{planTitle}'. Next billing date is {nextDate}.", array(
          '{siteName}'  => Yii::app()->name,
          '{email}'     => $this->model->email,
          '{planTitle}' => $plan->title,
          '{nextDate}'  => $params['date'],
        ));
        break;
      default:
        $description = Yii::t('account', "{siteName} account '{email}' upgrade to plan '{planTitle}'. Next billing date is {nextDate}.", array(
          '{siteName}'  => Yii::app()->name,
          '{email}'     => $this->model->email,
          '{planTitle}' => $plan->title,
          '{nextDate}'  => $params['date'],
        ));
    }

    $this->renderPartial('checkout',array(
      'gatewayUrl'   => Yii::app()->params['paymentGateway']['url'],
      'gatewayLogin' => Yii::app()->params['paymentGateway']['login'],
      'amount'       => $invoice->amount,
      'invoice'      => $invoice->id,
      'description'  => $description,
      'signature'    => $invoice->signature,
      'currency'     => $invoice->currency,
      'email'        => $this->model->email,
      'language'     => Yii::app()->language,
    ));
    Yii::app()->end();
  }

  public function actionResult()
  {
    $amount = Yii::app()->request->getParam('OutSum');
    $idInvoice = Yii::app()->request->getParam('InvId');
    $signature = Yii::app()->request->getParam('SignatureValue');
    $invoice = Invoice::model()->findByPk(intval($idInvoice));

    if (!Yii::app()->request->isPostRequest || $invoice == null || $invoice->status != Invoice::STATUS_WAITING) {
      $message = date('Y-m-d H:i:s') . PHP_EOL;
      $message .= 'Invalid usage for accounting detected:' . PHP_EOL;
      $message .= 'GET: ' . print_r($_GET, true) . PHP_EOL;
      $message .= 'POST: ' . print_r($_POST, true) . PHP_EOL;
      $message .= 'SERVER: ' . print_r($_SERVER, true) . PHP_EOL;
      $message .= '-----------------------------------------------------' . PHP_EOL . PHP_EOL;
      file_put_contents(Yii::app()->runtimePath . DIRECTORY_SEPARATOR . 'accounting.log', $message, FILE_APPEND);
      throw new CHttpException(400, Yii::t('error','Invalid usage detected. Your activity has been logged for futher investigation.'));
    }

    $mySignature = md5($amount . ':' . $idInvoice . ':' . Yii::app()->params['paymentGateway']['password2']);

    if (strtoupper($signature) === strtoupper($mySignature)) {
      $invoice->result($signature,$amount,Invoice::STATUS_SUCCESS);

      $user = User::model()->findByPk($invoice->userID);
      $user->idPricingPlan = $invoice->planID;
      $user->paidTill = $invoice->paidTill;
      $user->save();

      // @todo add email confirmation about account upgrade
    }
    else {
      $invoice->result($signature,$amount, Invoice::STATUS_FAIL);
    }
  }

  public function actionSuccess()
  {
    $this->render('success');
  }

  public function actionFail()
  {
    $this->render('fail');
  }

  public function expireAfter($date)
  {
    $expire = strtotime($date);

    return Yii::t('common', '{n} day left|{n} days left', array(round(($expire - time()) / 86400)));
  }

  private function calculator($plan, $cycle)
  {
    $debit = round((strtotime($this->model->paidTill) - time()) / 86400);
    if ($debit > 0) {
      switch ($this->model->billing) {
        case PricingPlan::BILLING_ANNUALLY:
          $debit = $this->model->plan->pricePerYear / 365 * $debit;
          break;
        default:
          $debit = $this->model->plan->pricePerMonth / 30.5 * $debit;
      }
    }
    else {
      $debit = 0;
    }

    $billing = array();
    foreach ($plan->getBillingOptions() as $value => $name) {
      $class = array('value'=>$value);
      if ($value == $cycle) {
        $class['selected'] = 'selected';
      }
      $billing[] = CHtml::tag('option',$class,$name);
    }

    switch ($cycle) {
      case PricingPlan::BILLING_ANNUALLY:
        $price = $plan->pricePerYear;
        $days = 365;
        break;
      default:
        $price = $plan->pricePerMonth;
        $days = 30.5;
    }

    if ($debit > $price) {
      $charge = 0;
      $price = $price / $days;
      $time = time() + $debit / $price * 86400;
    }
    else {
      $charge = $price - $debit;
      $time = time() + $days * 86400;
    }

    return array(
      'plan'    => $plan->id,
      'cycle'   => $cycle,
      'billing' => $billing,
      'charge'  => CurrencyHelper::render($charge),
      'time'    => $time,
      'date'    => Yii::app()->format->formatDate($time),
      'amount'  => $charge,
    );
  }

  public function translateTimeZones($timezones)
  {
    $translated = Yii::app()->cache->get('TimeZones.' . Yii::app()->language);

    if ($translated == null) {
      $translated = array();
      foreach ($timezones as $timezone => $label) {
        $translated[$timezone] = Yii::t('timezones', $label);
      }
      Yii::app()->cache->set('TimeZones.' . Yii::app()->language,$translated,86400);
    }

    return $translated;
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'account/index':
        $title = Yii::t('titles', 'Account Options');
        break;
      case 'account/profile':
        $title = Yii::t('titles', 'Account Profile');
        break;
      case 'account/nameserver':
        $title = Yii::t('titles', 'Nameservers Options');
        break;
      case 'account/upgrade':
        $title = Yii::t('titles', 'Account Upgrade');
        break;
      case 'account/success':
        $title = Yii::t('titles', 'Upgraded Successfully');
        break;
      case 'account/fail':
        $title = Yii::t('titles', 'Unsuccessful Upgrade');
        break;
      case 'account/remove':
        $title = Yii::t('titles', 'Account Removal');
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
}
