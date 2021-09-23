<?php
/**
  Project       : ActiveDNS
  Document      : controllers/ConfigController.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Application configuration management controller
*/
class ConfigController extends Controller
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

  public function actionIndex()
  {
    $r = Yii::app()->request;
    $model = new Config;

    if ($r->isPostRequest) {
      foreach ($r->getParam(get_class($model)) as $id => $value) {
        $model = Config::set($id, $value);
        if ($model->hasErrors()) {
          Yii::app()->user->setFlash('error', Yii::t('error','An error occurred while configuration update: {error}', array('{error}' => $this->getFirstError($model))));
          break;
        }
      }

      if (!Yii::app()->user->hasFlash('error')) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Site configuration updated successfully'));
      }
    }

    $this->render('index', array(
      'model' => $model,
    ));
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'config/index':
        $title = Yii::t('titles','Site Configuration');
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
