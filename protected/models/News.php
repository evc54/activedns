<?php
/**
  Project       : ActiveDNS
  Document      : models/News.php
  Document type : PHP script file
  Created at    : 07.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Latest news model
*/
/**
 * @property integer $id
 * @property integer $idUser author
 * @property boolean $public published
 * @property string  $create news created at
 * @property string  $update news updated at
 * @property string  $publish news piblished at
 */
class News extends CActiveRecord
{
  const PAGESIZE = 25;
  const INDEX_PAGESIZE = 1;
  
  private $_andContent = false;

  /**
   * @param string $className
   * @return News
   */
  public static function model($className = __CLASS__)
  {
    return parent::model($className);
  }

  public function tableName()
  {
    return '{{' . __CLASS__ . '}}';
  }

  public function rules()
  {
    return array(
      array('idUser,create,update,publish', 'numerical', 'integerOnly' => true),
      array('public', 'boolean'),
      array('public,idAuthor,create', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'author'                 => array(self::BELONGS_TO, 'User', 'idUser'),
      'content'                => array(self::HAS_MANY, 'NewsContent', 'idNews'),
      'currentLanguageContent' => array(self::HAS_ONE, 'NewsContent', 'idNews', 'condition' => 'currentLanguageContent.language=:language', 'params' => array(':language' => Yii::app()->language)),
    );
  }

  public function beforeSave()
  {
    if (parent::beforeSave()) {
      if ($this->isNewRecord) {
        $this->create = time();
      }
      
      if ($this->public && empty($this->publish)) {
        $this->publish = time();
      }
      
      $this->idUser = Yii::app()->user->id;
      $this->update = time();

      return true;
    }

    return false;
  }

  public function afterSave()
  {
    if ($this->_andContent && $this->content) {
      foreach ($this->content as $content) {
        if (empty($content->idNews)) {
          $content->idNews = $this->id;
        }
        $content->save();
      }
    }

    parent::afterSave();
  }

  public function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if ($this->_andContent && $this->content) {
        foreach ($this->content as $content) {
          $content->delete();
        }
      }

      return true;
    }

    return false;
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'idUser' => Yii::t('news', 'Author'),
      'public' => Yii::t('news', 'Published'),
      'create' => Yii::t('news', 'Created at'),
      'publish' => Yii::t('news', 'Published at'),
      'update' => Yii::t('news', 'Updated at'),
    );
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->with = array('author', 'currentLanguageContent');
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('author.realname', $this->idUser);
    $criteria->compare('author.id', $this->idUser, false, 'OR');

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.create') . ' DESC',
      ),
    ));
  }

  public function latest($limit = self::INDEX_PAGESIZE)
  {
    $criteria = new CDbCriteria;
    $criteria->with = array('currentLanguageContent');
    $criteria->compare('t.public', '>0');
    $criteria->limit = $limit;

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => $limit),
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.publish') . ' DESC',
      ),
    ));
  }

  public function index()
  {
    $criteria = new CDbCriteria;
    $criteria->with = array('currentLanguageContent');
    $criteria->compare('t.public', '>0');

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array('pageSize' => self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => Yii::app()->db->quoteColumnName('t.publish') . ' DESC',
      ),
    ));
  }

  public function publish()
  {
    $this->public = true;

    return $this->save();
  }

  public function hide()
  {
    $this->public = false;

    return $this->save();
  }

  public function andContent()
  {
    $this->_andContent = true;
    
    return $this;
  }

  public function getContent($language)
  {
    if ($this->content) {
      foreach ($this->content as $content) {
        if ($content->language == $language) {
          $model = $content;
        }
      }
    }

    if (empty($model)) {
      $model = new NewsContent;
      $model->idNews = $this->id;
      $model->language = $language;
    }
    
    return $model;
  }
}
