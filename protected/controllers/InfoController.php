<?php
/**
  Project       : ActiveDNS
  Document      : controllers/InfoController.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News management controller
*/
class InfoController extends Controller
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
            'publish',
            'hide',
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
      'ajaxActionMassPublish',
      'ajaxActionMassHide',
      'ajaxActionMassRemove',
    );
  }

  public function actionAjax($ajax)
  {
    $method = strpos($ajax,'ajaxAction') !== false ? $ajax : ('ajaxAction' . ucfirst($ajax));
    if (Yii::app()->request->isAjaxRequest && method_exists($this, $method) && in_array($method, $this->getAjaxMethods())) {
      return $this->$method();
    }
    else {
      if (Yii::app()->request->getParam('news')) {
        $news = Yii::app()->request->getParam('news');
        while (is_array($news)) {
          $news = current($news);
        }
        $this->redirect($this->createUrl('update', array('id' => $news)));
      }
      $this->redirect($this->createUrl('index'));
    }
  }

  public function ajaxActionMassPublish()
  {
    $news = Yii::app()->request->getParam('news', array());
    $affected = 0;
    foreach ($news as $id) {
      $entry = News::model()->findByPk(intval($id));
      if ($entry->publish()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('news', '{n} news published successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassHide()
  {
    $news = Yii::app()->request->getParam('news', array());
    $affected = 0;
    foreach ($news as $id) {
      $entry = News::model()->findByPk(intval($id));
      if ($entry->hide()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('news', '{n} news hidden successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemove()
  {
    $news = Yii::app()->request->getParam('news', array());
    $affected = 0;
    foreach ($news as $id) {
      $entry = News::model()->findByPk(intval($id));
      if ($entry->andContent()->delete()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('news', '{n} news removed successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function actionIndex()
  {
    $r = Yii::app()->request;
    $model = new News('search');
    $model->unsetAttributes();
    $model->attributes = $r->getParam(get_class($model), array());

    if ($r->isAjaxRequest) {
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
    $model = new News('create');

    if ($r->isPostRequest) {
      if ($this->updateModel($r, $model)) {
        $model->andContent()->save();
        Yii::app()->user->setFlash('success', Yii::t('news', 'News entry ID {id} created successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      } else {
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
      if ($this->updateModel($r, $model)) {
        $model->andContent()->save();
        Yii::app()->user->setFlash('success', Yii::t('news', 'News entry ID {id} updated successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      } else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->renderUpdate($model);
  }

  private function renderUpdate($model)
  {
    foreach (array('create', 'publish', 'update') as $attribute) {
      $model->setAttribute($attribute, Yii::app()->format->formatDatetime($model->getAttribute($attribute)));
    }
    
    if (!empty($model->author)) {
      $model->idUser = $model->author->realname;
    }
    
    $this->render('update', array(
      'model' => $model,
    ));
  }

  private function updateModel($r, $model)
  {
    $dataIsValid = true;
    $model->attributes = $r->getParam(get_class($model),array());
    $dataIsValid = $dataIsValid || $model->validate();
    $newsContent = array();
    foreach ($r->getParam('content') as $code => $content) {
      $tmpContent = $model->getContent($code);
      $tmpContent->attributes = $content;
      $dataIsValid = $dataIsValid || $tmpContent->validate();
      $newsContent[] = $tmpContent;
    }
    $model->content = $newsContent;

    return $dataIsValid;
  }

  public function actionDelete($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->delete()) {
        Yii::app()->user->setFlash('success', Yii::t('news', 'News entry ID {id} removed successfully', array('{id}' => $id)));
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

  public function actionPublish($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->publish()) {
        Yii::app()->user->setFlash('success', Yii::t('news', 'News entry ID {id} published successfully',array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('publish', array(
      'model' => $model,
    ));
  }

  public function actionHide($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->hide()) {
        Yii::app()->user->setFlash('success', Yii::t('news', 'News entry ID {id} hidden successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('hide', array(
      'model' => $model,
    ));
  }

  private function loadModel($id)
  {
    $model = News::model()->findByPk(intval($id));

    if ($model == null) {
      throw new CHttpException(404, Yii::t('news', 'News entry was not found'));
    }

    return $model;
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'info/index':
        $title = Yii::t('titles', 'News Management');
        break;
      case 'info/create':
        $title = Yii::t('titles', 'New News Entry');
        break;
      case 'info/update':
        $title = Yii::t('titles', 'Update News Entry');
        break;
      case 'info/piblish':
        $title = Yii::t('titles', 'Publish News Entry');
        break;
      case 'info/hide':
        $title = Yii::t('titles', 'Hide News Entry');
        break;
      case 'info/delete':
        $title = Yii::t('titles', 'Delete News Entry');
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
