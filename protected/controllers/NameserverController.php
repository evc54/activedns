<?php
/**
  Project       : ActiveDNS
  Document      : controllers/NameserverController.php
  Document type : PHP script file
  Created at    : 20.10.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameservers management controller
*/
class NameserverController extends Controller
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
    if (Yii::app()->user->isGuest) {
      $this->redirect('site/index');
    }

    return parent::beforeAction($action);
  }

  public function getAjaxMethods()
  {
    return array(
      'ajaxActionMassDisable',
      'ajaxActionMassEnable',
      'ajaxActionMassRemove',
      'ajaxActionPairReload',
    );
  }

  public function actionAjax($ajax)
  {
    $method = strpos($ajax,'ajaxAction') !== false ? $ajax : ('ajaxAction' . ucfirst($ajax));
    if (Yii::app()->request->isAjaxRequest && method_exists($this,$method) && in_array($method,$this->getAjaxMethods())) {
      return $this->$method();
    }
    else {
      if (Yii::app()->request->getParam('nameservers')) {
        $nameservers = Yii::app()->request->getParam('nameservers');
        while (is_array($nameservers)) {
          $nameservers = current($nameservers);
        }
        $this->redirect($this->createUrl('update', array('id'=>$nameservers)));
      }
      $this->redirect($this->createUrl('index'));
    }
  }

  public function ajaxActionMassDisable()
  {
    $nameservers = Yii::app()->request->getParam('nameservers', array());
    $affected = 0;
    foreach ($nameservers as $id) {
      $nameserver = NameServer::model()->findByPk(intval($id));
      if ($nameserver->disable()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} nameserver disabled successfully|{n} nameservers disabled successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassEnable()
  {
    $nameservers = Yii::app()->request->getParam('nameservers', array());
    $affected = 0;
    foreach ($nameservers as $id) {
      $nameserver = NameServer::model()->findByPk(intval($id));
      if ($nameserver->enable()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} nameserver enabled successfully|{n} nameservers enabled successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemove()
  {
    $nameservers = Yii::app()->request->getParam('nameservers', array());
    $affected = 0;
    foreach ($nameservers as $id) {
      $nameserver = NameServer::model()->findByPk(intval($id));
      if ($nameserver->delete()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} nameserver removed successfully|{n} nameservers removed successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionPairReload()
  {
    $id = Yii::app()->request->getParam('id');
    $type = Yii::app()->request->getParam('type');
    $criteria = new CDbCriteria;
    $criteria->compare('type', '<>' . $type);
    $criteria->compare('id', '<>' . $id);
    $data = NameServer::model()->findAll($criteria);
    echo CJSON::encode(CHtml::listData($data, 'id', 'name'));
    Yii::app()->end();
  }

  public function actionIndex()
  {
    $model = new NameServer('search');
    $model->unsetAttributes();
    if (isset($_GET['NameServer'])) $model->attributes = $_GET['NameServer'];

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
    $model = new NameServer('create');

    if (isset($_POST['NameServer'])) {
      $model->attributes = $_POST['NameServer'];
      if ($model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('nameserver', 'Nameserver ID {id} created successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      } else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('update', array(
      'model' => $model,
      'pairs' => CHtml::listData(
        NameServer::model()->findAll(Yii::app()->db->quoteColumnName('type') . '<>:type', array(':type' => NameServer::TYPE_MASTER)),
        'id',
        'name'
      ),
    ));
  }

  public function actionUpdate($id)
  {
    $model = $this->loadModel($id);

    if (isset($_POST['NameServer'])) {
      $model->attributes = $_POST['NameServer'];
      if ($model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('nameserver', 'Nameserver ID {id} updated successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      } else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('update', array(
      'model' => $model,
      'pairs' => CHtml::listData(
        NameServer::model()->findAll(Yii::app()->db->quoteColumnName('type') . '<>:type', array(':type' => $model->type)),
        'id',
        'name'
      ),
    ));
  }

  public function actionDelete($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->delete()) {
        Yii::app()->user->setFlash('success', Yii::t('nameserver','Nameserver ID {id} removed successfully', array('{id}' => $id)));
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
        Yii::app()->user->setFlash('success', Yii::t('nameserver', 'Nameserver ID {id} disabled successfully', array('{id}' => $model->id)));
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
        Yii::app()->user->setFlash('success', Yii::t('nameserver', 'Nameserver ID {id} enabled successfully', array('{id}' => $model->id)));
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
    $model = NameServer::model()->findByPk(intval($id));

    if ($model == null) {
      throw new CHttpException(404, Yii::t('nameserver', 'Nameserver was not found'));
    }

    return $model;
  }
  
  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'nameserver/index':
        $title = Yii::t('titles', 'Nameservers Management');
        break;
      case 'nameserver/create':
        $title = Yii::t('titles', 'New Nameserver');
        break;
      case 'nameserver/update':
        $title = Yii::t('titles', 'Update Nameserver');
        break;
      case 'nameserver/enable':
        $title = Yii::t('titles', 'Enable Nameserver');
        break;
      case 'nameserver/disable':
        $title = Yii::t('titles', 'Disable Nameserver');
        break;
      case 'nameserver/delete':
        $title = Yii::t('titles', 'Delete Nameserver');
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
