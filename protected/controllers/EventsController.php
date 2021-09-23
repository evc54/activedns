<?php
/**
  Project       : ActiveDNS
  Document      : controllers/EventsController.php
  Document type : PHP script file
  Created at    : 08.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Events view controller
*/
class EventsController extends Controller
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
          'users' => array('@'),
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

  public function afterAction($action)
  {
    Yii::app()->user->setEventSeen(time());
  }

  public function actionIndex()
  {
    $model = new DomainEvent('search');
    $model->unsetAttributes();
    $model->attributes = Yii::app()->request->getParam(get_class($model),array());

    if (Yii::app()->request->isAjaxRequest) {
      $this->renderPartial('grid', array(
        'model' => $model,
      ));
      Yii::app()->end();
    }

    $this->registerCommonScripts();
    $this->render('index', array(
      'model' => $model,
    ));
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'events/index':
        $title = Yii::t('titles', 'Browse Events');
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
