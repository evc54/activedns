<?php
/**
    Project       : ActiveDNS
    Document      : controllers/PlanController.php
    Document type : PHP script file
    Created at    : 05.01.2013
    Author        : Eugene V Chernyshev <evc22rus@gmail.com>
    Description   : Pricing plans management controller
*/
class PlanController extends Controller
{
  public $layout = '//layouts/backend';

  public function accessRules()
  {
    return CMap::mergeArray(
      parent::accessRules(),
      array(
        array(
          'allow',
          'actions' => array(
            'index',
            'create',
            'update',
            'disable',
            'enable',
            'delete',
            'ajax',
          ),
          'roles' => array('admin'),
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
    if (parent::beforeAction($action)) {
      if (Yii::app()->user->isGuest) {
        $this->redirect('site/index');
      }
      
      return true;
    }

    return false;
  }

  public function getAjaxMethods()
  {
    return array(
      'ajaxActionMassDisable',
      'ajaxActionMassEnable',
      'ajaxActionMassRemove',
      'ajaxActionSlaveReload',
    );
  }

  public function actionAjax($ajax)
  {
    $method = strpos($ajax,'ajaxAction') !== false ? $ajax : ('ajaxAction' . ucfirst($ajax));
    if (Yii::app()->request->isAjaxRequest && method_exists($this, $method) && in_array($method, $this->getAjaxMethods())) {
      return $this->$method();
    }
    else {
      if (Yii::app()->request->getParam('plans')) {
        $plans = Yii::app()->request->getParam('plans');
        while (is_array($plans)) {
          $plans = current($plans);
        }
        $this->redirect($this->createUrl('update', array('id' => $plans)));
      }
      $this->redirect($this->createUrl('index'));
    }
  }

  public function ajaxActionMassDisable()
  {
    $plans = Yii::app()->request->getParam('plans', array());
    $affected = 0;
    foreach ($plans as $id) {
      $plan = PricingPlan::model()->findByPk(intval($id));
      if ($plan->disable()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} plan disabled successfully|{n} plans disabled successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassEnable()
  {
    $plans = Yii::app()->request->getParam('plans', array());
    $affected = 0;
    foreach ($plans as $id) {
      $plan = PricingPlan::model()->findByPk(intval($id));
      if ($plan->enable()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} plan enabled successfully|{n} plans enabled successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemove()
  {
    $plans = Yii::app()->request->getParam('plans', array());
    $affected = 0;
    foreach ($plans as $id) {
      $plan = PricingPlan::model()->findByPk(intval($id));
      if ($plan->delete()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} plan removed successfully|{n} plans removed successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }
  
  public function ajaxActionSlaveReload()
  {
    $id = Yii::app()->request->getParam('id');
    $criteria = new CDbCriteria;
    $criteria->compare('type', NameServer::TYPE_SLAVE);
    $criteria->compare('idNameserverPair', $id);
    $ns = NameServer::model()->findAll($criteria);
    foreach ($ns as $nameserver) {
      $json[$nameserver->id] = $nameserver->name;
    }
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function actionIndex()
  {
    $model = new PricingPlan('search');
    $model->unsetAttributes();
    if (isset($_GET['PricingPlan'])) {
      $model->attributes = $_GET['PricingPlan'];
    }

    if (Yii::app()->request->isAjaxRequest) {
      $this->renderPartial('grid', array('model' => $model));
      Yii::app()->end();
    }

    $this->cs->registerScriptFile($this->scriptUrl('dialogs'));
    if (Yii::app()->language != 'en') {
      $this->cs->registerScriptFile($this->scriptUrl('dialogs-locale-' . Yii::app()->language));
    }
    $this->cs->registerScriptFile($this->scriptUrl('filter-clear-fields'));
    $this->registerCommonScripts();
    Yii::app()->bootstrap->registerModal();
    $this->render('index', array(
      'model' => $model,
    ));
  }

  public function actionCreate()
  {
    $r = Yii::app()->request;
    $model = new PricingPlan('create');

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam('PricingPlan');
      if ($model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Pricing plan ID {id} created successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->renderUpdate($model);
  }

  public function actionUpdate($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam('PricingPlan');
      if ($model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Pricing plan ID {id} updated successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->renderUpdate($model);
  }

  private function renderUpdate($model)
  {
    $nameservers = NameServer::model()->getMasterNameservers();
    $masterNameservers = array();
    foreach ($nameservers as $id => $nameserver) {
      $masterNameservers[$id] = $nameserver['name'];
    }
    $this->render('update', array(
      'model'             => $model,
      'masterNameservers' => $masterNameservers,
      'nameservers'       => $nameservers,
    ));
  }

  public function actionDelete($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->delete()) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Pricing plan ID {id} removed successfully', array('{id}' => $id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('delete', array(
      'model' => $model,
    ));
  }

  public function actionDisable($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->disable()) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Pricing plan ID {id} disabled successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('disable', array(
      'model' => $model,
    ));
  }

  public function actionEnable($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->enable()) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Pricing plan ID {id} enabled successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('enable', array(
      'model' => $model,
    ));
  }

  private function loadModel($id)
  {
    $model = PricingPlan::model()->findByPk(intval($id));

    if ($model == null) {
      throw new CHttpException(404, Yii::t('error', 'Pricing plan was not found'));
    }

    return $model;
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'plan/index':
        $title = Yii::t('titles', 'Pricing Plans Management');
        break;
      case 'plan/create':
        $title = Yii::t('titles', 'New Pricing Plan');
        break;
      case 'plan/update':
        $title = Yii::t('titles', 'Update Pricing Plan');
        break;
      case 'plan/enable':
        $title = Yii::t('titles', 'Enable Pricing Plan');
        break;
      case 'plan/disable':
        $title = Yii::t('titles', 'Disable Pricing Plan');
        break;
      case 'plan/delete':
        $title = Yii::t('titles', 'Delete Pricing Plan');
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
