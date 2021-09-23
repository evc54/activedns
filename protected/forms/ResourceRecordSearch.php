<?php
/**
  Project       : ActiveDNS
  Document      : ResourceRecordSearch.php
  Document type : PHP script file
  Created at    : 09.01.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Resource record search form
*/
class ResourceRecordSearch extends CFormModel
{
  public $query;
  public $result;

  public function rules()
  {
    return array(
      array('query', 'required'),
      array('query', 'length', 'max' => 1024)
    );
  }

  public function attributeLabels()
  {
    return array(
      'query' => Yii::t('forms', 'Search query'),
    );
  }
}
