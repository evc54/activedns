<?php
/**
  Project       : ActiveDNS
  Document      : controllers/TemplateController.php
  Document type : PHP script file
  Created at    : 12.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Template management controller
*/
class TemplateController extends Controller
{
  public $layout = '//layouts/backend';

  public function accessRules()
  {
    return CMap::mergeArray(
      parent::accessRules(),
      array(
        array(
          'allow',
          'actions'=> array(
            'index',
            'common',
            'create',
            'view',
            'rename',
            'update',
            'delete',
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
      'ajaxActionCreateRR',
      'ajaxActionUpdateRR',
      'ajaxActionRemoveRR',
      'ajaxActionMassRemove',
      'ajaxActionMassRemoveRR',
    );
  }

  public function actionAjax($ajax)
  {
    $method = strpos($ajax,'ajaxAction') !== false ? $ajax : ('ajaxAction' . ucfirst($ajax));
    if (Yii::app()->request->isAjaxRequest && method_exists($this, $method) && in_array($method, $this->getAjaxMethods())) {
      return $this->$method();
    }
    else {
      if (Yii::app()->request->getParam('templates')) {
        $templates = Yii::app()->request->getParam('templates');
        while (is_array($templates)) {
          $templates = current($templates);
        }
        $this->redirect($this->createUrl('update', array('id' => $templates)));
      }
      $this->redirect($this->createUrl('index'));
    }
  }

  public function ajaxActionCreateRR()
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($r->getParam('id'));

    $type = $r->getParam('type');

    $tr = new TemplateRecord('createType' . strtoupper($type));
    $tr->unsetAttributes();
    $tr->templateID = $model->id;

    $view = '/domain/rr/modals/' . $type;

    $json = array();
    if ($r->isPostRequest) {
      $tr->type = strtoupper($type);
      foreach ($_POST as $attribute => $value) {
        if (in_array($attribute,array('id', 'type'))) {
          continue;
        }
        if ($tr->hasAttribute($attribute)) {
          $tr->setAttribute($attribute, $value);
        }
      }

      if ($tr->save()) {
        $json['success'] = Yii::t('success', 'Success');
        $json['message'] = Yii::t('success', 'Template record successfully created');
        $json['grid'] = 'rrgrid-' . strtoupper($type);
      }
      else {
        $json['error'] = true;
      }
    }
    else {
      $json['title'] = Yii::t('template', 'Create a new template record type {type}', array('{type}' => strtoupper($type)));
    }

    if ($tr->hasErrors() || !$r->isPostRequest) {
      $json['content'] = $this->getViewFile($view)
        ? $this->renderPartial($view, array(
            'model' => $model,
            'rr'    => $tr,
            'id'    => 'modal-create',
          ), true)
        : CHtml::tag('h4', array(), Yii::t('template', 'Invalid type of template record provided'));
      $json['message'] = $this->getFirstError($tr);
    }

    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionUpdateRR()
  {
    $id = Yii::app()->request->getParam('id');
    $tr = TemplateRecord::model()->findByPk(intval($id));
    $json = array();
    if (!($tr && $tr->template && (Yii::app()->user->getModel()->role != User::ROLE_ADMIN ? $tr->template->idUser == Yii::app()->user->id : true))) {
      throw new CHttpException(404, Yii::t('error', 'Not found'));
    }
    $model = $tr->template;

    $view = '/domain/rr/modals/' . strtolower($tr->type);
    if (Yii::app()->request->isPostRequest) {
      foreach ($_POST as $attribute => $value) {
        if (in_array($attribute,array('id', 'type'))) {
          continue;
        }
        if ($tr->hasAttribute($attribute)) {
          $tr->setAttribute($attribute,$value);
        }
      }

      if ($tr->save()) {
        $json['success'] = Yii::t('success', 'Success');
        $json['message'] = Yii::t('success', 'Template record successfully updated');
        $json['grid'] = 'rrgrid-' . $tr->type;
      }
      else {
        $json['error'] = true;
      }
    }
    else {
      $json['title'] = Yii::t('template', 'Update template record type {type}', array('{type}' => $tr->type));
    }
    if ($tr->hasErrors() || !Yii::app()->request->isPostRequest) {
      $json['content'] = $this->getViewFile($view)
        ? $this->renderPartial($view,array(
            'model' => $model,
            'rr'    => $tr,
            'id'    => 'modal-update',
          ), true)
        : CHtml::tag('h4', array(), Yii::t('error', 'Invalid type of template record provided'));
    }
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionRemoveRR()
  {
    $id = Yii::app()->request->getParam('id');
    $tr = TemplateRecord::model()->findByPk(intval($id));
    if (!($tr && $tr->template && $tr->template->idUser == Yii::app()->user->id)) {
      throw new CHttpException(404, Yii::t('error', 'Not found'));
    }
    $grid = 'rrgrid-' . $tr->type;

    if ($tr->delete()) {
      $json = array(
        'success' => Yii::t('success', 'Success'),
        'message' => Yii::t('success', 'Template record removed successfully'),
        'grid'    => $grid,
      );
    }
    else {
      $error = $this->getFirstError($tr);
      $json = array(
        'error'   => Yii::t('error', 'Error'),
        'message' => empty($error) ? Yii::t('error', 'An error occurred while processing') : $error,
      );
    }
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemoveRR()
  {
    $json = array();
    $rrs = Yii::app()->request->getParam('rr', array());
    $affected = 0;
    foreach ($rrs as $record) {
      $tr = TemplateRecord::model()->findByPk(intval($record));
      if (!($tr && $tr->template && $tr->template->idUser == Yii::app()->user->id)) {
        continue;
      }
      if ($tr->delete()) {
        $affected++;
      }
    }
    $json['success'] = Yii::t('success', 'Success');
    $json['message'] = Yii::t('success', '{n} template record removed successfully|{n} template records removed successfully', array($affected));
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function ajaxActionMassRemove()
  {
    $templates = Yii::app()->request->getParam('templates', array());
    $affected = 0;
    foreach ($templates as $id) {
      $template = $this->loadModel($id, false);
      if ($template !== null && $template->delete()) {
        $affected++;
      }
    }
    $json = array(
      'success' => Yii::t('success', 'Success'),
      'message' => Yii::t('success', '{n} template removed successfully|{n} templates removed successfully', array($affected)),
    );
    echo CJSON::encode($json);
    Yii::app()->end();
  }

  public function actionIndex()
  {
    $r = Yii::app()->request;
    $model = new Template('search');
    $model->unsetAttributes();
    $model->attributes = $r->getParam(get_class($model), array());
    $model->type = Template::TYPE_PRIVATE;

    if ($r->isAjaxRequest) {
      $this->renderPartial('grid', array(
        'model' => $model,
      ));
      Yii::app()->end();
    }

    $this->renderIndex($model);
  }

  public function actionCommon()
  {
    $r = Yii::app()->request;
    $model = new Template('search');
    $model->unsetAttributes();
    $model->attributes = $r->getParam(get_class($model), array());
    $model->type = Template::TYPE_COMMON;

    if ($r->isAjaxRequest) {
      $this->renderPartial('grid', array(
        'model' => $model,
      ));
      Yii::app()->end();
    }

    $this->renderIndex($model);
  }

  private function renderIndex($model)
  {
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
    $model = new Template('create');
    $this->name($model);
  }

  public function actionRename($id)
  {
    $model = $this->loadModel($id);
    $this->name($model);
  }

  private function name($model)
  {
    $r = Yii::app()->request;

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam(get_class($model), array());
      if (in_array(Yii::app()->user->getRole(), array(User::ROLE_USER))) {
        $model->type = Template::TYPE_PRIVATE;
      }
      if ($model->save()) {
        $this->redirect($this->createUrl('update', array('id'=>$model->id)));
      }
      else {
        Yii::app()->user->setFlash('error',$this->getFirstError($model));
      }
    }

    $this->render('name', array(
      'model' => $model,
    ));
  }

  public function actionView($id)
  {
    $model = $this->loadModelView($id);

    $this->render('view', array(
      'model' => $model,
    ));
  }

  public function actionUpdate($id)
  {
    $r = Yii::app()->request;
    $model = $this->loadModel($id);

    if ($r->isAjaxRequest) {
      $ajax = str_replace('rrgrid-', '', $r->getParam('ajax'));
      if (in_array($ajax, $this->getRRTypes())) {
        $this->renderPartial('/domain/rr/grid', array(
          'rr'    => TemplateRecord::model()->search($model->id, $ajax),
          'model' => $model,
          'type'  => $ajax,
        ));
      }
      Yii::app()->end();
    }

    if ($r->isPostRequest) {
      $model->attributes = $r->getParam(get_class($model), array());
      if ($model->save()) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Template ID {id} updated successfully', array('{id}' => $model->id)));
        $this->redirect($this->createUrl('index'));
      }
      else {
        Yii::app()->user->setFlash('error', $this->getFirstError($model));
      }
    }

    Yii::app()->bootstrap->registerModal();
    Yii::app()->bootstrap->registerButton();
    $this->cs->registerScriptFile($this->scriptUrl('dialogs'));
    if (Yii::app()->language != 'en') {
      $this->cs->registerScriptFile($this->scriptUrl('dialogs-locale-' . Yii::app()->language));
    }
    $this->render('update', array(
      'model' => $model,
    ));
  }

  public function actionDelete($id)
  {
    $model = $this->loadModel($id);

    if (Yii::app()->request->isPostRequest) {
      if ($model->delete()) {
        Yii::app()->user->setFlash('success', Yii::t('success', 'Template ID {id} removed successfully', array('{id}' => $id)));
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

  private function loadModel($id, $throwException = true)
  {
    if (in_array(Yii::app()->user->getRole(), array(User::ROLE_ADMIN))) {
      $model = Template::model()->findByPk(intval($id));
    }
    else {
      $model = Template::model()->own()->findByPk(intval($id));
    }

    if ($throwException && $model == null) {
      throw new CHttpException(404, Yii::t('error', 'Template was not found'));
    }

    return $model;
  }

  private function loadModelView($id)
  {
    $model = Template::model()->findByPk(intval($id));

    if ($model == null) {
      throw new CHttpException(404, Yii::t('error', 'Template was not found'));
    }

    return $model;
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
  
  public function getTitle($route)
  {
    $title = '';

    switch ($route) {
      case 'template/index':
        $title = Yii::t('titles', 'Templates Management');
        break;
      case 'template/common':
        $title = Yii::t('titles', 'Common Templates');
        break;
      case 'template/create':
        $title = Yii::t('titles', 'New Template');
        break;
      case 'template/update':
        $title = Yii::t('titles', 'Update Template');
        break;
      case 'template/rename':
        $title = Yii::t('titles', 'Rename Template');
        break;
      case 'template/delete':
        $title = Yii::t('titles', 'Template Delete Confirmation');
        break;
      case 'template/view':
        $title = Yii::t('titles', 'View Template');
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
