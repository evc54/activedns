<?php
/**
  Project       : ActiveDNS
  Document      : controllers/PanelController.php
  Document type : PHP script file
  Created at    : 08.05.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : User control panel controller
*/
class PanelController extends Controller
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

  public function actionIndex()
  {
    $model = Yii::app()->user->getModel();

    $this->render('index', array(
      'maxDomainsQty'    => $model->role != User::ROLE_ADMIN ? $model->getMaxDomainsQty() : '&infin;',
      'activeDomainsQty' => $model->role != User::ROLE_ADMIN ? $model->activeDomainsQty : Domain::model()->active()->count(),
      'totalAlertsQty'   => $model->role != User::ROLE_ADMIN ? $model->totalAlertsQty : Alert::model()->count(),
      'expiringDomains'  => $model->getExpiringDomains(),
      'stats'            => $model->getStats(),
      'lastEvents'       => DomainEvent::model()->index(),
      'news'             => new News('search'),
    ));
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'panel/index':
        $title = Yii::t('titles', 'Dashboard');
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
