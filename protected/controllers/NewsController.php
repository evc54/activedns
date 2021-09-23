<?php
/**
  Project       : ActiveDNS
  Document      : controllers/NewsController.php
  Document type : PHP script file
  Created at    : 08.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : News viewer controller
*/
class NewsController extends Controller
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
            'read',
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

  public function actionIndex()
  {
    $model = new News('search');
    $model->unsetAttributes();
    $model->attributes = Yii::app()->request->getParam(get_class($model), array());

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

  public function actionRead($id)
  {
    $model = News::model()->with(array('currentLanguageContent'))->findByPk(intval($id));

    if ($model == null || !$model->public) {
      throw new CHttpException(404, Yii::t('yii', 'Not found.'));
    }

    $more = array();

    // previous
    $criteria = new CDbCriteria;
    $criteria->with = array('currentLanguageContent');
    $criteria->compare('t.public', '>0');
    $criteria->compare('t.id', '<>' . $model->id);
    $criteria->compare('t.publish', '<=' . $model->publish);
    $more['previous'] = News::model()->find($criteria);

    // next
    $criteria = new CDbCriteria;
    $criteria->with = array('currentLanguageContent');
    $criteria->compare('t.public', '>0');
    $criteria->compare('t.id', '<>' . $model->id);
    $criteria->compare('t.publish', '>=' . $model->publish);
    $more['next'] = News::model()->find($criteria);
    
    $this->render('read', array(
      'model' => $model,
      'more'  => $more,
    ));
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'news/index':
        $title = Yii::t('titles', 'Browse News');
        break;
      case 'news/read':
        $title = Yii::t('titles', 'News Entry');
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
