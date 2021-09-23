<?php
/**
  Project       : ActiveDNS
  Document      : controllers/DomainController.php
  Document type : PHP script file
  Created at    : 09.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Domain management controller
*/
class DomainController extends Controller
{
  public $layout = '//layouts/backend';

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
            'add',
            'report',
            'update',
            'delete',
            'enable',
            'disable',
            'check',
            'replicate',
            'nameserver',
            'apply',
            'cancel',
            'transfer',
            'client',
            'export',
            'import',
            'diagnose',
            'expire',
            'search',
            'ajax',
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

  public function getAjaxMethods()
  {
    return array(
      'ajaxActionCreateRR',
      'ajaxActionUpdateRR',
      'ajaxActionRemoveRR',
      'ajaxActionUpdateSOA',
      'ajaxActionUpdateInfo',
      'ajaxActionMassRemoveRR',
      'ajaxActionMassEnableDomain',
      'ajaxActionMassDisableDomain',
      'ajaxActionMassRemoveDomain',
      'ajaxActionMassApplyTemplate',
      'ajaxActionMassApplyZone',
      'ajaxActionMassChangeNameservers',
      'ajaxActionMassCheckDomain',
      'ajaxActionCreateTransferEntry',
      'ajaxActionUpdateTransferEntry',
      'ajaxActionRemoveTransferEntry',
      'ajaxActionMassRemoveEntry',
    );
  }

  public function actionIndex()
  {
    $r = Yii::app()->request;
    $model = new Domain('search');
    $model->unsetAttributes();
    $model->attributes = $r->getParam(get_class($model), array());

    if ($pageSize = $r->getParam('size')) {
      Yii::app()->user->setState('DomainsPerPage', $pageSize);
    }

    if ($r->isAjaxRequest && $r->getParam('ajax')) {
      $this->renderPartial('grids/manage', array(
        'model' => $model,
      ));
      Yii::app()->end();
    }

    $this->cs->registerScriptFile($this->scriptUrl('jquery.sparkline'));
    $this->cs->registerScriptFile($this->scriptUrl('dialogs'));
    if (Yii::app()->language != 'en') {
      $this->cs->registerScriptFile($this->scriptUrl('dialogs-locale-' . Yii::app()->language));
    }
    $this->registerCommonScripts();
    $this->render('index', array(
      'model'     => $model,
      'templates' => $this->getTemplates(),
    ));
  }

  public function actionAdd()
  {
    $max = Yii::app()->user->getModel()->getMaxDomainsQty();
    if ($max > 0 && Domain::model()->own()->count() >= $max) {
      Yii::app()->user->setFlash('error', Yii::t('error', '<strong>Warning!</strong> You are already added maximum of allowed number of domains ({number}). If you need more, please think about upgrading your account', array('{number}' => $max)));
      $this->redirect('index');
    }

    $r = Yii::app()->request;

    if ($r->isPostRequest) {
      $domains = $r->getParam('domains');
      $domains = strtr($domains, array("\t" => ' ', "\n" => ' ', "\r" => ' ', "," => ' '));
      $domains = explode(' ', $domains);
      $domains = array_filter($domains);
      Yii::app()->user->setFlash('domains', $domains);
      Yii::app()->user->setFlash('services', $r->getParam('services', false));
      Yii::app()->user->setFlash('templates', $r->getParam('templates', array()));

      $nameservers = $r->getParam('nameservers',array());
      if (!empty($nameservers) && is_array($nameservers)) {
        while (is_array($nameservers)) {
          $nameservers = current($nameservers);
        }
      }
      Yii::app()->user->setFlash('nameservers', $nameservers);

      $this->redirect(array('report'));
    }

    $this->render('add');
  }

  public function actionReport()
  {
    $user = Yii::app()->user->getModel();
    $max = $user->getMaxDomainsQty();
    $number = Domain::model()->own()->count();
    $r = Yii::app()->request;
    $domains = Yii::app()->user->getFlash('domains');
    $services = Yii::app()->user->getFlash('services');
    $templates = Yii::app()->user->getFlash('templates');
    $nameservers = Yii::app()->user->getFlash('nameservers');

    if ($r->isAjaxRequest) {
      $domain = $r->getParam('domain');
      $services = $r->getParam('services');
      $templates = explode(',', $r->getParam('templates'));
      $nameservers = $r->getParam('nameservers');
      if (!empty($nameservers)) {
        $nameservers = NameServerAlias::model()->filterByUser()->findByPk(intval($nameservers));
      }

      $domain = \Iodev\Whois\Helpers\DomainHelper::toAscii(strtolower($domain));
      $encodedDomain = \Iodev\Whois\Helpers\DomainHelper::toUnicode($domain);
      $domain = mb_convert_case($domain, MB_CASE_UPPER, Yii::app()->charset);
      $sld = array_reverse(explode('.', $domain));

      if (!preg_match('/^([a-zA-Z0-9-\.]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9-])?\.)+[a-zA-Z0-9-]+$/', $domain)) {
        $report = array(
          'class'  => 'error',
          'domain' => $domain,
          'result' => Yii::t('error', 'Invalid domain name'),
        );
      }
      elseif ($max > 0 && $number >= $max) {
        $report = array(
          'class'  => 'error',
          'domain' => $domain,
          'result' => Yii::t('error', 'Exceeded maximum allowed number of domains'),
        );
      }
      elseif (count($sld) > 1 && !$this->checkSecondLevelDomain($sld[1] . '.' . $sld[0])) {
        $report = array(
          'class'  => 'error',
          'domain' => $domain,
          'result' => Yii::t('error', 'You can\'t add this domain since you aren\'t own second level domain'),
        );
      }
      else {
        $model = new Domain;
        $model->idUser = Yii::app()->user->id;
        $model->name = $encodedDomain;
        $model->status = Domain::DOMAIN_WAITING;

        if ($model->save()) {
          $number++;
          $model->logEvent(DomainEvent::TYPE_DOMAIN_CREATED);
          $checker = new EDomainChecker($model, true);
          $checker->info();
          if ($model->register == null && $model->expire == null && $model->getLevel() > 2) {
            $model->allowAutoCheck = false;
          }
          $model->status = Domain::DOMAIN_UPDATE;
          $model->save();

          $zone = new Zone;
          if ($nameservers instanceof NameServerAlias) {
            $zone->idNameServerAlias = $nameservers->id;
          }
          $zone->serial = date('Ymd01');
          $zone->hostmaster = Yii::app()->user->getAttribute('soaHostmaster');
          $zone->refresh = Yii::app()->user->getAttribute('soaRefresh');
          $zone->retry = Yii::app()->user->getAttribute('soaRetry');
          $zone->expire = Yii::app()->user->getAttribute('soaExpire');
          $zone->minimum = Yii::app()->user->getAttribute('soaMinimum');
          $zone->idDomain = $model->id;
          $zone->save();
          $model->idZoneCurrent = $zone->id;
          $model->save();

          if ($services) {
            foreach ($model->getCommonServicesList() as $host) {
              $check = $host ? $host . '.' . $encodedDomain : $encodedDomain;
              if ($address = gethostbynamel($check)) {
                foreach ($address as $ip) {
                  if ($ip !== $check) {
                    $rr = new ResourceRecord;
                    $rr->idZone = $zone->id;
                    $rr->host = $host ? $host : '@';
                    $rr->class = ResourceRecord::DEFAULT_CLASS;
                    $rr->type = ResourceRecord::TYPE_A;
                    $rr->rdata = $ip;
                    $rr->ttl = $zone->minimum;
                    $rr->save();
                  }
                }
              }
            }

            if (getmxrr($encodedDomain,$mx,$weights)) {
              foreach ($mx as $key => $host) {
                if ($host) {
                  if (strpos(strrev($host),strrev($encodedDomain . '.')) === 0) {
                    $host = str_replace('.' . $encodedDomain . '.','',$host);
                    $address = gethostbyname($host);
                    if ($address != $host) {
                      $rr = new ResourceRecord;
                      $rr->idZone = $zone->id;
                      $rr->host = $host;
                      $rr->class = ResourceRecord::DEFAULT_CLASS;
                      $rr->type = ResourceRecord::TYPE_A;
                      $rr->rdata = $address;
                      $rr->ttl = $zone->minimum;
                      $rr->save();
                    }
                  }
                  else {
                    $host .= '.';
                  }
                  $rr = new ResourceRecord;
                  $rr->idZone = $zone->id;
                  $rr->host = '@';
                  $rr->class = ResourceRecord::DEFAULT_CLASS;
                  $rr->type = ResourceRecord::TYPE_MX;
                  $rr->rdata = $host;
                  $rr->priority = $weights[$key];
                  $rr->ttl = $zone->minimum;
                  $rr->save();
                }
              }
            }
          }

          if (!empty($nameservers)) {
            $ns = array();
            $ns[$nameservers->idNameServerMaster] = $nameservers->NameServerMasterAlias;
            $ns[$nameservers->idNameServerSlave1] = $nameservers->NameServerSlave1Alias;
            if ($user->plan->nameserversQty > 2 && !empty($ns[$nameservers->idNameServerSlave2]) && !empty($ns[$nameservers->idNameServerSlave3])) {
              $ns[$nameservers->idNameServerSlave2] = $nameservers->NameServerSlave2Alias;
              $ns[$nameservers->idNameServerSlave3] = $nameservers->NameServerSlave3Alias;
            }
            $nameservers = $ns;
          }
          else {
            $nameservers = array();
            for ($i = 1; $i <= $user->plan->nameserversQty; $i++) {
              $ns = NameServer::model()->findByPk($user->getAttribute('ns' . $i));
              if (!empty($ns)) {
                $nameservers[$ns->id] = $ns->name;
              }
            }
          }

          foreach ($nameservers as $nameserverID => $nameserverName) {
            $bind = new ZoneNameServer;
            $bind->zoneID = $zone->id;
            $bind->nameServerID = $nameserverID;
            $bind->save();

            $rr = new ResourceRecord;
            $rr->idZone = $zone->id;
            $rr->host = '@';
            $rr->class = ResourceRecord::DEFAULT_CLASS;
            $rr->type = ResourceRecord::TYPE_NS;
            $rr->rdata = $nameserverName . '.';
            $rr->ttl = $zone->minimum;
            $rr->readonly = true;
            $rr->save();
          }

          if ($templates) {
            foreach ($templates as $templateID) {
              $model->template($templateID, $zone, Template::PRIORITY_TEMPLATE);
            }
          }

          $report = array(
            'class'  => 'success',
            'domain' => $domain,
            'result' => Yii::t('success','Domain added'),
          );
        }
        else {
          $report = array(
            'class'  => 'error',
            'domain' => $domain,
            'result' => $this->getFirstError($model),
          );
        }
      }

      echo CJSON::encode($report);
      Yii::app()->end();
    }

    if (empty($domains)) {
      $this->redirect(array('index'));
    }

    $this->render('report', array(
      'domains'     => $domains,
      'services'    => $services,
      'templates'   => implode(',', $templates),
      'nameservers' => $nameservers,
    ));
  }

  protected function checkSecondLevelDomain($domain)
  {
    if (Yii::app()->user->getRole() != User::ROLE_ADMIN) {
      $model = Domain::model()->findByAttributes(array('name' => $domain));
    }

    return empty($model) ? true : $model->idUser == Yii::app()->user->id;
  }

  public function actionUpdate($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    $zone = Zone::model()->findByPk(intval($r->getParam('zone') ? $r->getParam('zone') : Yii::app()->user->getState($model->id . '.current.zone', $model->idZoneCurrent)));
    if ($zone == null) {
      $zone = $model->makeNewZone();
    }
    Yii::app()->user->setState($model->id . '.current.zone', $zone->id);

    if ($r->isAjaxRequest) {
      $ajax = str_replace('rrgrid-', '', $r->getParam('ajax'));
      switch ($ajax) {
        case 'soa':
          $this->renderPartial('soa', array(
            'model' => $model,
            'zone'  => $zone,
          ));
          break;
        case 'info':
          $this->renderPartial('info', array(
            'model' => $model,
            'zone'  => $zone,
          ));
          break;
        case ResourceRecord::TYPE_A:
        case ResourceRecord::TYPE_AAAA:
        case ResourceRecord::TYPE_CNAME:
        case ResourceRecord::TYPE_PTR:
        case ResourceRecord::TYPE_MX:
        case ResourceRecord::TYPE_SRV:
        case ResourceRecord::TYPE_NS:
        case ResourceRecord::TYPE_TXT:
          $this->renderPartial('rr/grid', array(
            'rr'    => ResourceRecord::model()->search($zone->id, $ajax),
            'model' => $model,
            'type'  => $ajax,
          ));
          break;
      }
      if ($r->getParam('zone')) {
        $selector = $this->renderPartial('selectors/zone', array(
          'model' => $model,
          'zone'  => $zone,
          'ajax'  => $r->getParam('ajax'),
        ),true);
        switch ($r->getParam('ajax')) {
          case 'transfer':
            echo CJSON::encode(array(
              'transfer' => $this->renderPartial('grids/transfer', array(
                  'model' => $model,
                  'zone'  => $zone,
                ), true),
              'selector' => $selector,
            ));
            break;
          case 'editor':
          default:
            echo CJSON::encode(array(
              'editor' => $this->renderPartial('rr/resource', array(
                  'model' => $model,
                  'zone'  => $zone,
                ), true),
              'soa' => $this->renderPartial('soa', array(
                  'model' => $model,
                  'zone' => $zone,
                ), true),
              'info' => $this->renderPartial('info', array(
                  'model'=>$model,
                  'zone'=>$zone,
                ), true),
              'selector' => $selector,
              'current' => $zone->id == $model->currentZone->id,
            ));
        }
      }
      Yii::app()->end();
    }

    Yii::app()->bootstrap->registerModal();
    Yii::app()->bootstrap->registerButton();
    $this->cs->registerScriptFile($this->scriptUrl('dialogs'));
    if (Yii::app()->language != 'en') {
      $this->cs->registerScriptFile($this->scriptUrl('dialogs-locale-' . Yii::app()->language));
    }
    $this->registerCommonScripts();
    $this->render('update', array(
      'model' => $model,
      'zone'  => $zone,
    ));
  }

  public function actionApply($id)
  {
    $model = $this->loadModel($id);
    $zone = Zone::model()->findByPk(intval(Yii::app()->user->getState($model->id . '.current.zone')));
    if (
      ($zone == null)
      || ($zone->domain == null)
      || (Yii::app()->user->getModel()->role != User::ROLE_ADMIN && $zone->domain->idUser != Yii::app()->user->id)
    ) {
      throw new CHttpException(404, Yii::t('common','Not found'));
    }

    if ($zone->serial > 0) {
      $zone = $model->makeNewZone($zone);
    }

    $zone->serial = $model->getNextSerial();
    $zone->domain->idZoneCurrent = $zone->id;
    $zone->save();
    $zone->domain->replicate();
    $zone->domain->logEvent(DomainEvent::TYPE_DOMAIN_CHANGE_ZONE, array('{serial}' => $zone->serial));
    Yii::app()->user->setFlash('success',Yii::t('success','New zone successfully applied to domain'));
    Yii::app()->user->setState($model->id . '.current.zone', $zone->id);
    $this->redirect($this->createUrl('update', array('id' => $zone->idDomain)));
  }

  public function actionCancel($id)
  {
    $model = $this->loadModel($id);
    $zone = $model->newZone;
    $zone->delete();
    Yii::app()->user->setFlash('success', Yii::t('success', 'Zone update successfully cancelled'));
    Yii::app()->user->setState($model->id . '.current.zone', $model->idZoneCurrent);
    $this->redirect($this->createUrl('update',array('id' => $model->id)));
  }

  public function actionTransfer($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isAjaxRequest) {
      $this->renderPartial('grids/transfer', array(
        'model' => $model,
      ));

      Yii::app()->end();
    }

    $this->cs->registerScriptFile($this->scriptUrl('dialogs'));
    if (Yii::app()->language != 'en') {
      $this->cs->registerScriptFile($this->scriptUrl('dialogs-locale-' . Yii::app()->language));
    }
    $this->render('transfer',array(
      'model' => $model,
    ));
  }

  public function actionClient($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);
    $error = false;
    $email = $r->getParam('client', '');

    if ($r->isPostRequest) {
      if (!empty($email)) {
        $client = User::model()->findByAttributes(array('email' => $email));
        if ($client != null) {
          Domain::model()->updateAll(array('idUser' => $client->id), "id=:id", array(':id' => $model->id));
          Alert::model()->updateAll(array('idUser' => $client->id), "idDomain=:id", array(':id' => $model->id));
          DomainEvent::model()->updateAll(array('idUser'=>$client->id), "idDomain=:id", array(':id' => $model->id));
          $this->redirect(array('index'));
        }
        else {
          $error = 'not-found';
        }
      }
      else {
        $error = 'empty';
      }
    }

    $this->render('client', array(
      'model'     => $model,
      'email'     => $email,
      'error'     => $error,
      'returnUrl' => $this->createUrl('update', array('id' => $id)),
    ));
  }

  public function actionExport($id)
  {
    $zone = Zone::model()->findByPk($id);
    if (
      ($zone == null)
      || ($zone->domain == null)
      || (Yii::app()->user->getModel()->role != User::ROLE_ADMIN && $zone->domain->idUser != Yii::app()->user->id)
    ) {
      throw new CHttpException(404, Yii::t('common', 'Not found'));
    }

    $content = $zone->generateZoneRRFile();
    header('Content-Disposition: attachment; filename=' . $zone->domain->getDomainName() . '.conf');
    header('Last-Modified: ' . date('D, d M Y H:i:s T', 1));
    header('Content-Length: ' . strlen($content));
    header('Content-Type: text/plain');

    echo $content;

    Yii::app()->end();
  }

  public function actionImport($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isPostRequest) {
      $file = CUploadedFile::getInstanceByName('zonefile');
      if ($file) {
        $parser = new ResourceRecordParser($model->getDomainName(), $file->tempName);
        $result = $parser->parse();
        if (empty($result) || empty($result['soa']['domain'])) {
          $error = Yii::t('error', "Uploaded file seems to isn't zone file.");
        }
        elseif (strtoupper($result['soa']['domain']) != strtoupper($model->getDomainName())) {
          $error = Yii::t('error','Uploaded file hold data for another domain "{domain}".', array('{domain}' => $result['soa']['domain']));
        }
        else {
          $nameservers = array();
          $zone = $model->newZone ? $model->newZone : $model->makeNewZone($model->currentZone);
          if ($zone->record) {
            foreach ($zone->record as $record) {
              if ($record->readonly && $record->type == ResourceRecord::TYPE_NS) {
                $nameservers[] = $record->rdata;
              }
              else {
                $record->delete();
              }
            }
          }

          if (!empty($result['soa'])) {
            foreach (array('hostmaster', 'refresh', 'retry', 'expire', 'minimum') as $attribute) {
              if (!empty($result['soa'][$attribute])) {
                $zone->setAttribute($attribute, $result['soa'][$attribute]);
              }
            }
            $zone->save();
          }

          $c = 0;
          foreach ($result['rr'] as $record) {
            if (!empty($record['type']) && !empty($record['done'])) {
              if (!empty($record['rdata']) && $record['type'] == ResourceRecord::TYPE_NS && in_array($record['rdata'], $nameservers)) {
                continue;
              }
              $rr = new ResourceRecord;
              $rr->idZone = $zone->id;
              foreach ($record as $attribute => $data) {
                $rr->setAttribute($attribute,$data);
              }
              if ($rr->save()) {
                $c++;
              }
            }
          }

          Yii::app()->user->setState($model->id . '.current.zone', $zone->id);
        }
      }
    }

    $this->render('import',array(
      'model'  => $model,
      'error'  => empty($error) ? null : $error,
      'report' => empty($result['report']) ? null : $result['report'],
      'c'      => empty($c) ? 0 : $c,
      'info'   => array(
        'directive'      => Yii::t('domain', 'Global directive'),
        'soa'            => Yii::t('domain', 'Start of Authority block'),
        'resourcerecord' => Yii::t('domain', 'Resource Record'),
        'unsupported'    => Yii::t('domain', 'Unsupported Resource Record'),
        'invalid'        => Yii::t('domain', 'Invalid line'),
        'skipped'        => Yii::t('domain', 'Skipped line'),
      ),
    ));
  }

  public function actionDelete($id, $inner = false)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isPostRequest) {
      if ($model->remove()) {
        $model->logEvent(DomainEvent::TYPE_DOMAIN_REMOVING);
        Yii::app()->user->setFlash('success', Yii::t('success', 'Domain {domain} has been removed', array('{domain}' => $model->name)));
      }
      else {
        Yii::app()->user->setFlash('error', Yii::t('error', 'Domain could not be removed: {error}', array('{error}' => $this->getFirstError($model))));
      }

      $this->redirect($r->getParam('returnUrl', $this->createUrl('index')));
    }

    $this->render('delete', array(
      'model'     => $model,
      'returnUrl' => $this->createReturnUrl('enable', $model->id, $inner),
    ));
  }

  public function actionEnable($id, $inner = false)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isPostRequest) {
      if ($model->enable()) {
        $model->logEvent(DomainEvent::TYPE_DOMAIN_ENABLED);
        Yii::app()->user->setFlash('success', Yii::t('success','Domain {domain} has been enabled', array('{domain}' => $model->name)));
      }
      else {
        Yii::app()->user->setFlash('error', Yii::t('error','Domain could not be enabled: {error}', array('{error}' => $this->getFirstError($model))));
      }

      $this->redirect($r->getParam('returnUrl', $this->createUrl('index')));
    }

    $this->render('enable', array(
      'model'     => $model,
      'returnUrl' => $this->createReturnUrl('enable', $model->id, $inner),
    ));
  }

  public function actionDisable($id, $inner = false)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isPostRequest) {
      if ($model->disable()) {
        $model->logEvent(DomainEvent::TYPE_DOMAIN_DISABLED);
        Yii::app()->user->setFlash('success', Yii::t('success', 'Domain {domain} has been disabled', array('{domain}' => $model->name)));
      }
      else {
        Yii::app()->user->setFlash('error', Yii::t('error', 'Domain could not be disabled: {error}', array('{error}' => $this->getFirstError($model))));
      }

      $this->redirect($r->getParam('returnUrl', $this->createUrl('index')));
    }

    $this->render('disable', array(
      'model'     => $model,
      'returnUrl' => $this->createReturnUrl('disable', $model->id, $inner),
    ));
  }

  public function actionNameserver($id, $inner = false)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if (!empty($model->newZone)) {
      $nameServerAliasID = $model->newZone->idNameServerAlias;
    }
    elseif(!empty($model->currentZone)) {
      $nameServerAliasID = $model->currentZone->idNameServerAlias;
    }
    else {
      $nameServerAliasID = '';
    }

    if ($r->isPostRequest) {
      $nameservers = $r->getParam('nameservers',0);
      if (is_array($nameservers)) {
        while (is_array($nameservers)) {
          $nameservers = current($nameservers);
        }
      }
      $apply = $r->getParam('apply',false);

      if ($model->changeNS($nameservers,$apply)) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Nameservers for domain {domain} has been changed', array('{domain}' => $model->name)));
      }
      else {
        Yii::app()->user->setFlash('error', Yii::t('error','Nameservers could not be changed: {error}', array('{error}' => $this->getFirstError($model))));
      }

      $this->redirect($r->getParam('returnUrl', $this->createUrl('index')));
    }

    $this->render('nameserver', array(
      'model'             => $model,
      'nameServerAliasID' => $nameServerAliasID,
      'returnUrl'         => $this->createReturnUrl('nameserver', $model->id, $inner),
    ));
  }

  public function actionCheck($id, $inner = false)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    $checker = new EDomainChecker($model, false, true);
    $model->lastAutoCheck = time();
    $model->save(false);

    Yii::app()->user->setFlash('success', Yii::t('success', 'Domain {domain} info has been refreshed',array('{domain}' => $model->name)));
    $this->redirect($this->createReturnUrl('check', $model->id, $inner));
  }

  public function actionReplicate($id, $inner = false)
  {
    $model = $this->loadModel($id);

    $model->status = Domain::DOMAIN_UPDATE;
    $model->idZoneReplicated = 0;
    $model->save(false);

    Yii::app()->user->setFlash('success', Yii::t('success', 'Domain {domain} has been forced to replicate',array('{domain}' => $model->name)));
    $this->redirect($this->createReturnUrl('check', $model->id, $inner));
  }

  private function createReturnUrl($action, $id, $inner)
  {
    $returnUrl = Yii::app()->request->urlReferrer;

    if ($inner) {
      $returnUrl = $this->createUrl('update', array('id' => $id));
    }
    elseif ($returnUrl == $this->createAbsoluteUrl($action, array('id' => $id))) {
      $returnUrl = $this->createUrl('index');
    }

    return $returnUrl;
  }

  public function actionDiagnose()
  {
    $r = Yii::app()->request;
    $model = new Domain('search');
    $model->unsetAttributes();
    $model->attributes = $r->getParam(get_class($model), array());

    if ($r->isAjaxRequest && $r->getParam('ajax')) {
      $this->renderPartial('grids/diagnose', array(
        'model' => $model,
      ));
      Yii::app()->end();
    }

    $this->render('diagnose', array(
      'model' => $model,
    ));
  }

  public function actionExpire()
  {
    $r = Yii::app()->request;
    $model = new Domain('search');
    $model->unsetAttributes();
    $model->attributes = $r->getParam(get_class($model),array());

    if ($r->isAjaxRequest && $r->getParam('ajax')) {
      $this->renderPartial('grids/expire', array(
        'model' => $model,
      ));
      Yii::app()->end();
    }

    $this->render('expire', array(
      'model' => $model,
    ));
  }

  public function actionSearch()
  {
    $r = Yii::app()->request;
    $model = new ResourceRecordSearch;

    if ($r->getParam('q')) {
      $model->query = $r->getParam('q');

      if ($model->validate()) {
        $criteria = new CDbCriteria;
        $criteria->compare('record.host', $model->query, true);
        $criteria->compare('record.rdata', $model->query, true, 'OR');
        $criteria->with = array(
          'currentZone',
          'currentZone.record',
        );
        $model->result = Domain::model()->own()->findAll($criteria);
      }
    }

    $this->render('search', array(
      'model' => $model,
    ));
  }

  public function actionAjax($ajax)
  {
    $method = strpos($ajax,'ajaxAction') !== false ? $ajax : ('ajaxAction' . ucfirst($ajax));
    if (Yii::app()->request->isAjaxRequest && method_exists($this,$method) && in_array($method, $this->getAjaxMethods())) {
      return $this->$method();
    }
    else {
      $this->redirect($this->createUrl('domain/index'));
    }
  }

  public function ajaxActionCreateRR()
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($r->getParam('id'));

    $type = $r->getParam('type');

    $rr = new ResourceRecord('createType' . strtoupper($type));
    $rr->unsetAttributes();

    $view = 'rr/modals/' . $type;

    $json = array();
    if ($r->isPostRequest) {
      $zone = Zone::model()->findByPk(intval(Yii::app()->user->getState($model->id . '.current.zone', $model->idZoneCurrent)));
      if ($zone->serial > 0) {
        if ($model->newZone !== null) {
          $model->newZone->delete();
        }
        $zone = $model->makeNewZone($zone);
        Yii::app()->user->setState($model->id . '.current.zone', $zone->id);
        $newZoneCreated = true;
      }

      $rr->idZone = $zone->id;
      $rr->type = strtoupper($type);
      switch ($rr->type) {
        case 'SPF':
          $rr->type = ResourceRecord::TYPE_TXT;
          $rr->setScenario('createTypeTXT');
          $rr->host = $r->getParam('host','@');
          $rr->ttl = $r->getParam('ttl',$zone->minimum);
          $replace = $r->getParam('replace',false);
          $txt = array();
          foreach (array('a','mx','include','address','all') as $attribute) {
            $value = $r->getParam($attribute);
            if (!empty($value)) {
              if ($value == 1) {
                $txt[] = $attribute;
              }
              elseif ($value == -1) {
                $txt[] = '-' . $attribute;
              }
              else {
                $list = explode("\n",str_replace(array("\r\n","\n","\t"), "\n", $value));
                if (count($list)) {
                  foreach ($list as $address) {
                    if (preg_match('/\d+\.\d+\.\d+\.\d+/', $address)) {
                      $txt[] = 'ip4:' . $address;
                    }
                    elseif (stripos($address,':')) {
                      $txt[] = 'ip6:' . $address;
                    }
                    else {
                      $txt[] = 'a:' . $address;
                    }
                  }
                }
              }
            }
          }
          $rr->rdata = 'v=spf1 ' . implode(' ', $txt);
          break;
        default:
          foreach ($_POST as $attribute => $value) {
            if (in_array($attribute, array('id', 'type', 'readonly'))) {
              continue;
            }
            if ($rr->hasAttribute($attribute)) {
              $rr->setAttribute($attribute, $value);
            }
          }
      }

      if (!empty($newZoneCreated)) {
        $json['reload'] = true;
        $json['url'] = $this->createUrl('update', array('id' => $model->id, 'zone' => $zone->id));
      }
      if ($rr->save()) {
        $json['success'] = Yii::t('success','Success');
        $json['message'] = Yii::t('success','Resource record successfully created');
        if (strtoupper($type) == 'SPF') {
          if (!empty($replace)) {
            $criteria = new CDbCriteria;
            $criteria->condition = "rdata LIKE 'v=spf1%'";
            $criteria->compare('id','<>' . $rr->id);
            $criteria->compare('idZone', $rr->idZone);
            $criteria->compare('type', ResourceRecord::TYPE_TXT);
            ResourceRecord::model()->deleteAll($criteria);
          }
          $json['grid'] = 'rrgrid-' . ResourceRecord::TYPE_TXT;
        }
        else {
          $json['grid'] = 'rrgrid-' . strtoupper($type);
        }
      }
      else {
        $json['error'] = true;
      }
    }
    else {
      switch (strtoupper($type)) {
        case 'SPF':
          $json['title'] = Yii::t('domain', 'New SPF record');
          break;
        default:
          $json['title'] = Yii::t('domain', 'New resource record type {type}', array('{type}' => strtoupper($type)));
      }
    }

    if ($rr->hasErrors() || !$r->isPostRequest) {
      if ($this->getViewFile($view)) {
        $json['content'] = $this->renderPartial($view, array(
          'model' => $model,
          'rr'    => $rr,
          'id'    => 'modal-create',
        ), true);
      }
      else {
        $json['content'] = CHtml::tag('h4', array(), Yii::t('error', 'Invalid type of resource record provided'));
        $json['error'] = true;
      }
      $json['message'] = $this->getFirstError($rr);
    }

    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionUpdateRR()
  {
    $id = Yii::app()->request->getParam('id');
    $rr = ResourceRecord::model()->findByPk(intval($id));
    $rr->setScenario('updateType' . $rr->type);

    $json = array();
    if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
      if (!($rr && $rr->zone && $rr->zone->domain && ($rr->zone->domain->idUser == Yii::app()->user->id))) {
        throw new CHttpException(404,Yii::t('common', 'Not found'));
      }
    }
    $model = $rr->zone->domain;
    $zone = $rr->zone;

    $view = 'rr/modals/' . strtolower($rr->type);
    if (Yii::app()->request->isPostRequest) {
      foreach ($_POST as $attribute => $value) {
        if (in_array($attribute,array('id', 'type', 'readonly'))) {
          continue;
        }
        if ($rr->hasAttribute($attribute)) {
          $rr->setAttribute($attribute,$value);
        }
      }

      if ($rr->validate()) {
        if ($zone->serial > 0) {
          if ($model->newZone !== null) {
            $model->newZone->delete();
          }
          $zone = $model->makeNewZone($zone, array($rr->id));
          Yii::app()->user->setState($model->id . '.current.zone', $zone->id);

          $json['reload'] = true;
          $json['url'] = $this->createUrl('update', array('id' => $model->id, 'zone' => $zone->id));

          $newRR = new ResourceRecord;
          $newRR->attributes = $rr->attributes;
          $newRR->id = null;
          $newRR->idZone = $zone->id;
          $newRR->save();
          $rr = $newRR;
        }
        else {
          $rr->save();
        }
        $json['success'] = Yii::t('success','Success');
        $json['message'] = Yii::t('success','Resource record successfully updated');
        $json['grid'] = 'rrgrid-' . $rr->type;
      }
      else {
        $json['error'] = true;
      }
    }
    else {
      $json['title'] = Yii::t('domain', 'Update resource record type {type}', array('{type}' => $rr->type));
    }
    if ($rr->hasErrors() || !Yii::app()->request->isPostRequest) {
      $json['content'] = $this->getViewFile($view)
        ? $this->renderPartial($view, array(
            'model' => $model,
            'rr'    => $rr,
            'id'    => 'modal-update',
          ), true)
        : CHtml::tag('h4', array(), Yii::t('common', 'Invalid type of resource record provided'));
    }
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionRemoveRR()
  {
    $json = array();
    $id = Yii::app()->request->getParam('id');
    $rr = ResourceRecord::model()->findByPk(intval($id));
    if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
      if (!($rr && $rr->zone && $rr->zone->domain && ($rr->zone->domain->idUser == Yii::app()->user->id))) {
        throw new CHttpException(404,Yii::t('common','Not found'));
      }
    }
    $grid = 'rrgrid-' . $rr->type;

    $success = false;
    $zone = $rr->zone;
    if ($zone->serial > 0) {
      if ($rr->zone->domain->newZone !== null) {
        $rr->zone->domain->newZone->delete();
      }
      $zone = $rr->zone->domain->makeNewZone($zone, array($rr->id));
      Yii::app()->user->setState($rr->zone->idDomain . '.current.zone', $zone->id);
      $json['reload'] = true;
      $json['url'] = $this->createUrl('update', array('id' => $rr->zone->idDomain, 'zone' => $zone->id));
      $success = true;
    }
    elseif ($rr->delete()) {
      $success = true;
    }
    if ($success) {
      $json['success'] = Yii::t('success','Success');
      $json['message'] = Yii::t('success','Resource record removed successfully');
      $json['grid'] = $grid;
    }
    else {
      $error = $this->getFirstError($rr);
      $json['error'] = Yii::t('error', 'Error');
      $json['message'] = empty($error) ? Yii::t('error', 'An error occurred while processing') : $error;
    }
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionUpdateSOA()
  {
    $json = array();
    $id = Yii::app()->request->getParam('id');
    $zone = Zone::model()->findByPk(intval($id));
    if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
      if (!($zone && $zone->domain && ($zone->domain->idUser == Yii::app()->user->id))) {
        throw new CHttpException(404, Yii::t('error', 'Not found'));
      }
    }
    $zone->setScenario('manual');
    $model = $zone->domain;
    $hostmaster = explode('.', rtrim($zone->hostmaster, ' .'));
    $zone->hostmaster = array_shift($hostmaster) . '@' . implode('.', $hostmaster);
    if (Yii::app()->request->isPostRequest) {
      if ($zone->serial > 0) {
        if ($model->newZone !== null) {
          $model->newZone->delete();
        }
        $zone = $model->makeNewZone($zone);
        Yii::app()->user->setState($model->id . '.current.zone', $zone->id);
        $json['reload'] = true;
        $json['url'] = $this->createUrl('update', array('id' => $model->id, 'zone' => $zone->id));
      }

      foreach (array(
        'refresh',
        'retry',
        'expire',
        'minimum',
      ) as $attribute) {
        $value = $_POST[$attribute];
        $multiplier = isset($_POST[$attribute . '-multiplier']) ? $_POST[$attribute . '-multiplier'] : 1;
        if ($zone->hasAttribute($attribute)) {
          $zone->setAttribute($attribute, ceil($value * $multiplier));
        }
      }
      $zone->setAttribute('hostmaster', $_POST['hostmaster']);

      if ($zone->validate() && $zone->save()) {
        $json['success'] = Yii::t('success', 'Success');
        $json['message'] = Yii::t('success', 'Start of authority successfully updated');
      }
      else {
        $json['error'] = true;
      }
    }
    else {
      $json['title'] = Yii::t('domain', 'Start of authority');
    }
    if ($zone->hasErrors() || !Yii::app()->request->isPostRequest) {
      $json['content'] = $this->renderPartial('rr/modals/soa', array(
          'model' => $model,
          'zone'  => $zone,
          'id'    => 'modal-update',
        ), true);
    }
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionUpdateInfo()
  {
    $r = Yii::app()->request;
    $json = array();
    $model = $this->loadModel($r->getParam('id'));
    if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
      if ($model->idUser != Yii::app()->user->id) {
        throw new CHttpException(404, Yii::t('error', 'Not found'));
      }
    }

    if ($r->isPostRequest) {
      $allowAutoCheck = $r->getParam('allowAutoCheck', 1);
      if (!$allowAutoCheck) {
        $model->allowAutoCheck = 0;
        foreach (array(
          'register',
          'expire',
          'ns1',
          'ns2',
          'ns3',
          'ns4',
          'registrar',
        ) as $attribute) {
          if ($model->hasAttribute($attribute)) {
            $model->setAttribute($attribute,$r->getParam($attribute,''));
          }
        }
        if ($model->status == Domain::DOMAIN_ALERT) {
          $model->status = Domain::DOMAIN_HOSTED;
          $model->clearAlerts();
        }
      }
      else {
        $model->allowAutoCheck = 1;
      }
      if ($model->validate() && $model->save()) {
        if ($model->allowAutoCheck == 1) {
          $checker = new EDomainChecker($model);
          $model->lastAutoCheck = time();
          $model->save(false);
        }
        $json['success'] = Yii::t('success', 'Success');
        $json['message'] = Yii::t('success', 'Domain information successfully updated');
      }
      else {
        $json['error'] = true;
      }
    }
    else {
      $json['title'] = Yii::t('domain', 'Domain info');
    }
    if ($model->hasErrors() || !Yii::app()->request->isPostRequest) {
      $json['content'] = $this->renderPartial('rr/modals/info', array(
          'model' => $model,
          'id'    => 'modal-update',
        ), true);
    }
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemoveRR()
  {
    $json = array();
    $rrs = Yii::app()->request->getParam('rr',array());
    $affected = 0;
    foreach ($rrs as $record) {
      $rr = ResourceRecord::model()->findByPk(intval($record));
      if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
        if (!($rr && $rr->zone && $rr->zone->domain && ($rr->zone->domain->idUser == Yii::app()->user->id))) {
          continue;
        }
      }
      if ($rr->zone->serial > 0) {
        $affected = count($rrs);
        $zone = $rr->zone->domain->makeNewZone($rr->zone, $rrs);
        $json['reload'] = true;
        $json['url'] = $this->createUrl('update', array('id' => $rr->zone->domain->id, 'zone' => $zone->id));
        break;
      }
      if ($rr->delete()) {
        $affected++;
      }
    }
    $json['success'] = Yii::t('success', 'Success');
    $json['message'] = Yii::t('success', '{n} resource record removed successfully|{n} resource records removed successfully', array($affected));
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassEnableDomain()
  {
    $domains = Yii::app()->request->getParam('domains', array());
    $affected = 0;
    foreach ($domains as $id) {
      $domain = Domain::model()->findByPk(intval($id));
      if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
        if ($domain == null || $domain->idUser != Yii::app()->user->id) {
          continue;
        }
      }
      if ($domain->enable()) {
        $domain->logEvent(DomainEvent::TYPE_DOMAIN_ENABLED);
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} domain enabled successfully|{n} domains enabled successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassDisableDomain()
  {
    $domains = Yii::app()->request->getParam('domains', array());
    $affected = 0;
    foreach ($domains as $id) {
      $domain = Domain::model()->findByPk(intval($id));
      if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
        if ($domain == null || $domain->idUser != Yii::app()->user->id) {
          continue;
        }
      }
      if ($domain->disable()) {
        $domain->logEvent(DomainEvent::TYPE_DOMAIN_DISABLED);
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} domain disabled successfully|{n} domains disabled successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemoveDomain()
  {
    $domains = Yii::app()->request->getParam('domains', array());
    $affected = 0;
    foreach ($domains as $id) {
      $domain = Domain::model()->findByPk(intval($id));
      if ($domain == null) {
        continue;
      }
      if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
        if ($domain->idUser != Yii::app()->user->id) {
          continue;
        }
      }
      if ($domain->remove()) {
        $domain->logEvent(DomainEvent::TYPE_DOMAIN_REMOVING);
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} domain removed successfully|{n} domains removed successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassApplyTemplate()
  {
    $r = Yii::app()->request;
    $domains = $r->getParam('domains', array());
    $templateID = $r->getParam('template');
    $priority = $r->getParam('priority');
    $apply = $r->getParam('apply');
    $affected = 0;
    foreach ($domains as $id) {
      $domain = Domain::model()->findByPk(intval($id));
      if ($domain == null) {
        continue;
      }
      if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
        if ($domain->idUser != Yii::app()->user->id) {
          continue;
        }
      }
      if ($domain->template($templateID, null, $priority, $apply)) {
        $affected++;
      }
      Yii::app()->user->setState($id . '.current.zone', $domain->newZone->id);
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', 'Chosen template successfully applied to {n} domain|Chosen template successfully applied to {n} domains', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassApplyZone()
  {
    $domains = Yii::app()->request->getParam('domains',array());
    $affected = 0;
    foreach ($domains as $id) {
      $domain = Domain::model()->with('newZone')->findByPk(intval($id));
      if ($domain == null || (Yii::app()->user->getModel()->role != User::ROLE_ADMIN && $domain->idUser != Yii::app()->user->id)) {
        continue;
      }
      if ($domain->newZone != null) {
        if ($domain->applyZone($domain->newZone)) {
          $affected++;
        }
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', 'Zone update successfully applied to {n} domain|Zone update successfully applied to {n} domains', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassChangeNameservers()
  {
    $r = Yii::app()->request;
    $domains = $r->getParam('domains', array());
    $idNameServerAlias = $r->getParam('nameservers', 0);
    $apply = $r->getParam('apply', false);
    $affected = 0;
    foreach ($domains as $id) {
      $domain = Domain::model()->findByPk(intval($id));
      if ($domain == null) {
        continue;
      }
      if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
        if ($domain->idUser != Yii::app()->user->id) {
          continue;
        }
      }
      if ($domain->changeNS($idNameServerAlias,$apply)) {
        $affected++;
      }
      Yii::app()->user->setState($id . '.current.zone', null);
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', 'Chosen nameservers successfully applied to {n} domain|Chosen nameservers successfully applied to {n} domains', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassCheckDomain()
  {
    $domains = Yii::app()->request->getParam('domains',array());
    $affected = 0;
    foreach ($domains as $id) {
      $domain = Domain::model()->findByPk(intval($id));
      if (Yii::app()->user->getModel()->role != User::ROLE_ADMIN) {
        if ($domain == null || $domain->idUser != Yii::app()->user->id) {
          continue;
        }
      }
      $checker = new EDomainChecker($domain);
      if ($domain->save()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} domain info checked successfully|{n} domains checked successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionCreateTransferEntry()
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($r->getParam('id'));
    $json = array();
    $success = false;
    $error = false;
    $entry = new DomainTransfer;

    if ($r->isPostRequest) {
      foreach (array('address', 'allowNotify', 'allowTransfer') as $attribute) {
        $entry->setAttribute($attribute, $r->getParam($attribute));
      }
      $entry->domainID = $model->id;
      if ($entry->save()) {
        $success = true;
      }
      else {
        $error = true;
      }
    }

    $json['success'] = $success;
    $json['error'] = $error;

    if (!$success) {
      $json['title'] = Yii::t('domain','Create zone transfer entry');
      $json['content'] = $this->renderPartial('modals/transfer', array(
        'model' => $entry,
      ), true);
    }

    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionUpdateTransferEntry()
  {
    $r = Yii::app()->request;
    $entry = DomainTransfer::model()->with('domain')->findByPk(intval($r->getParam('id')));
    if ($entry == null || (Yii::app()->user->getModel()->role == User::ROLE_USER && $entry->domain->idUser != Yii::app()->user->id)) {
      throw new CHttpException(404,Yii::t('error', 'Not found'));
    }
    $json = array();
    $success = false;
    $error = false;

    if ($r->isPostRequest) {
      foreach (array('address', 'allowNotify', 'allowTransfer') as $attribute) {
        $entry->setAttribute($attribute, $r->getParam($attribute));
      }
      if ($entry->save()) {
        $success = true;
      }
      else {
        $error = true;
      }
    }

    $json['success'] = $success;
    $json['error'] = $error;

    if (!$success) {
      $json['title'] = Yii::t('domain', 'Update zone transfer entry');
      $json['content'] = $this->renderPartial('modals/transfer', array(
        'model' => $entry,
      ), true);
    }

    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionRemoveTransferEntry()
  {
    $r = Yii::app()->request;
    $entry = DomainTransfer::model()->with('domain')->findByPk(intval($r->getParam('id')));
    if ($entry == null || (Yii::app()->user->getModel()->role == User::ROLE_USER && $entry->domain->idUser != Yii::app()->user->id)) {
      throw new CHttpException(404,Yii::t('error', 'Not found'));
    }
    $entry->delete();
    $json['success'] = Yii::t('success', 'Success');
    $json['message'] = Yii::t('success', 'Zone transfer entry has been removed');
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemoveEntry()
  {
    $json = array();
    $entries = Yii::app()->request->getParam('entries',array());
    $affected = 0;
    foreach ($entries as $entry) {
      $entry = DomainTransfer::model()->with('domain')->findByPk(intval($entry));
      if ($entry == null || (Yii::app()->user->getModel()->role == User::ROLE_USER && $entry->domain->idUser != Yii::app()->user->id)) {
        throw new CHttpException(404, Yii::t('error', 'Not found'));
      }
      if ($entry->delete()) {
        $affected++;
      }
    }
    $json['success'] = Yii::t('success', 'Success');
    $json['message'] = Yii::t('success', '{n} zone transfer entries removed successfully|{n} zone transfer entries removed successfully', array($affected));
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function getZoneMenu($model, $ajax = 'editor', $selectedZoneID = 0)
  {
    $menu = array();
    $menu[] = array('label'=>Yii::t('domain', 'select zone version'));

    $items = array();
    foreach ($model->zone as $zone) {
      $label = $zone->id == $model->idZoneCurrent ?
        Yii::t('domain', 'Serial {serial} (current)', array('{serial}' => $zone->serial)) :
        ($zone->serial == 0 ? Yii::t('domain', 'New (unapplied) zone') : Yii::t('common','Serial {serial}', array('{serial}' => $zone->serial)));

      if ($zone->id == $selectedZoneID) {
        $current = array(
          'label' => $label,
          'url'   => 'javascript:void(0)',
        );
      }
      else {
        $items[] = array(
          'label' => $label,
          'url'   => $this->createUrl('update', array('id' => $model->id, 'zone' => $zone->id, 'ajax' => $ajax)),
        );
      }
    }
    if (empty($current)) {
      $menu = CMap::mergeArray($menu,$items);
    }
    else {
      $current['items'] = $items;
      $menu[] = $current;
    }

    return $menu;
  }

  public function getRRTypes()
  {
    return array(
      ResourceRecord::TYPE_A,
      ResourceRecord::TYPE_AAAA,
      ResourceRecord::TYPE_CNAME,
      // ResourceRecord::TYPE_PTR,
      ResourceRecord::TYPE_MX,
      ResourceRecord::TYPE_SRV,
      ResourceRecord::TYPE_NS,
      ResourceRecord::TYPE_TXT,
    );
  }

  public function getPagination()
  {
    return array(
      5     => 5,
      10    => 10,
      25    => 25,
      50    => 50,
      100   => 100,
      500   => 500,
      'all' => 'all',
    );
  }

  private function getTemplates()
  {
    $templates = array();
    $model = Template::model()->select()->findAll();
    foreach ($model as $entry) {
      $templates[$entry->getAttributeLabelType()][$entry->id] = $entry->name;
    }

    return $templates;
  }

  private function loadModel($id)
  {
    $model = Domain::model()->own()->findByPk(intval($id));

    if ($model === null) {
      Yii::app()->user->setFlash('error', Yii::t('error','Domain was not found or access denied'));
      $this->redirect($this->createUrl('index'));
    }

    return $model;
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'domain/index':
        $title = Yii::t('titles', 'Domain Management');
        break;
      case 'domain/add':
        $title = Yii::t('titles', 'Add Domains');
        break;
      case 'domain/report':
        $title = Yii::t('titles', 'Processing Domains');
        break;
      case 'domain/update':
        $title = Yii::t('titles', 'Domain Zone Editor');
        break;
      case 'domain/delete':
        $title = Yii::t('titles', 'Delete Domain Confirmation');
        break;
      case 'domain/enable':
        $title = Yii::t('titles', 'Enable Domain Confirmation');
        break;
      case 'domain/disable':
        $title = Yii::t('titles', 'Disable Domain Confirmation');
        break;
      case 'domain/diagnose':
        $title = Yii::t('titles', 'Diagnose Domain Alerts');
        break;
      case 'domain/search':
        $title = Yii::t('titles', 'Search Over Zone Records');
        break;
      case 'domain/expire':
        $title = Yii::t('titles', 'Expiring Domains Forecast');
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
