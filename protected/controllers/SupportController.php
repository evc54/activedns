<?php
/**
  Project       : ActiveDNS
  Document      : controllers/SupportController.php
  Document type : PHP script file
  Created at    : 10.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Support controller
*/
class SupportController extends Controller
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
            'close',
            'reopen',
            'ajax',
          ),
          'users' => array('@'),
        ),
        array(
          'allow',
          'actions' => array(
            'process',
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
      'ajaxActionMassClose',
    );
  }

  public function actionAjax($ajax)
  {
    $method = strpos($ajax,'ajaxAction') !== false ? $ajax : ('ajaxAction' . ucfirst($ajax));
    if (Yii::app()->request->isAjaxRequest && method_exists($this, $method) && in_array($method, $this->getAjaxMethods())) {
      return $this->$method();
    }
  }

  public function ajaxActionMassClose()
  {
    $tickets = Yii::app()->request->getParam('tickets', array());
    $affected = 0;
    foreach ($tickets as $id) {
      $ticket = $this->loadModel($id,false);
      if ($ticket !== null && $ticket->close()) {
        $affected++;
      }
      else {
        var_dump($ticket->getErrors());
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} ticket closed successfully|{n} tickets closed successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function actionIndex()
  {
    $closed = Yii::app()->request->getParam('closed', null);
    if ($closed !== null) {
      Yii::app()->user->setState('Support.ShowClosed', $closed);
    }
    $showClosed = Yii::app()->user->getState('Support.ShowClosed', false);

    $model = new SupportTicket('search');
    $model->unsetAttributes();
    $model->attributes = Yii::app()->request->getParam(get_class($model), array());
    if (!$showClosed) {
      $model->hideClosed();
    }
    if (Yii::app()->user->getAttribute('role') != User::ROLE_ADMIN) {
      $model->authorID = Yii::app()->user->id;
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
      'model'      => $model,
      'showClosed' => $showClosed,
    ));
  }

  public function actionCreate()
  {
    $r = Yii::app()->request;
    $model = new SupportTicket('create');
    $reply = new SupportTicketReply('create');

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam(get_class($model), true);
      $reply->attributes = $r->getParam(get_class($reply), true);
      $model->authorID = Yii::app()->user->id;
      $reply->authorID = Yii::app()->user->id;
      $model->status = SupportTicket::STATUS_CREATED;

      if ($model->validate() && $reply->validate()) {
        $model->save();
        $reply->ticketID = $model->id;
        $reply->save();

        Yii::app()->user->setFlash('success', Yii::t('success', 'Ticket #{id} created successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        $error = $this->getFirstError($model->hasErrors() ? $model : $reply);
        Yii::app()->user->setFlash('error', $error);
      }
    }

    $this->render('create', array(
      'model' => $model,
      'reply' => $reply,
    ));
  }

  public function actionUpdate($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);
    $reply = new SupportTicketReply('create');
    $reply->ticketID = $model->id;
    $reply->authorID = Yii::app()->user->id;

    Yii::app()->user->setSupportSeen(max($model->created, $model->replied));

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam(get_class($model), true);
      $reply->attributes = $r->getParam(get_class($reply), true);

      if ($model->validate() && $reply->validate()) {
        $model->save();
        $reply->save();
        Yii::app()->user->setFlash('success', Yii::t('success', 'Ticket #{id} updated successfully', array('{id}' => $model->id)));
        if (isset($_POST['returnBack'])) {
          $this->redirect($this->createUrl('update', array('id' => $model->id)));
        }
        else {
          $this->redirect($this->createUrl('index'));
        }
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render('update', array(
      'model' => $model,
      'reply' => $reply,
    ));
  }

  public function actionProcess($id)
  {
    $this->request('process', $id, 'Ticket #{id} set in processing status successfully');
  }

  public function actionClose($id)
  {
    $this->request('close', $id, 'Ticket #{id} closed successfully');
  }

  public function actionReopen($id)
  {
    $this->request('reopen', $id, 'Ticket #{id} reopened successfully');
  }

  private function request($action, $id, $message)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if (method_exists($model, $action) && $model->$action()) {
        Yii::app()->user->setFlash('success',Yii::t('success', $message, array('{id}' => $id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    $this->render($action, array(
      'model' => $model,
    ));
  }

  private function loadModel($id, $throwException = true)
  {
    $model = SupportTicket::model()->own()->findByPk(intval($id));

    if ($throwException && $model == null) {
      throw new CHttpException(404, Yii::t('error','Ticket #{id} was not found', array('{id}' => $id)));
    }

    return $model;
  }

  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'support/index':
        $title = Yii::t('titles', 'Support');
        break;
      case 'support/create':
        $title = Yii::t('titles', 'New Ticket');
        break;
      case 'support/update':
        $title = Yii::t('titles', 'Update Ticket');
        break;
      case 'support/close':
        $title = Yii::t('titles', 'Ticket Close Confirmation');
        break;
      case 'support/reopen':
        $title = Yii::t('titles', 'Ticket Reopen Confirmation');
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
