<?php
/**
  Project       : ActiveDNS
  Document      : models/NewsContent.php
  Document type : PHP script file
  Created at    : 07.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Latest news multilanguage content model
*/
/**
 * @property integer $id
 * @property integer $idNews news entry
 * @property string  $language content language
 * @property string  $title title
 * @property string  $announce announce
 * @property string  $fulltext full text
 * @property boolean $concat concat annnounce with text when display full news entry
 */
class NewsContent extends CActiveRecord
{
  /**
   * @param string $className
   * @return NewsContent
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
      array('idNews,language', 'required'),
      array('idNews', 'numerical', 'integerOnly' => true),
      array('title', 'length', 'max' => 255),
      array('announce', 'length', 'max' => 1022),
      array('fulltext', 'length', 'max' => 65535),
      array('concat', 'boolean'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'title' => Yii::t('news', 'Title'),
      'announce' => Yii::t('news', 'Announce'),
      'fulltext' => Yii::t('news', 'Text'),
      'concat' => Yii::t('news', 'Concat announce with text'),
    );
  }
}
