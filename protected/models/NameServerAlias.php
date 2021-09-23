<?php
/**
  Project       : ActiveDNS
  Document      : models/NameServerAlias.php
  Document type : PHP script file
  Created at    : 05.05.2013
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Nameservers user's aliases model
*/
/**
 * @property integer $id primary key
 * @property integer $idUser owner ID
 * @property integer $idNameServerMaster master nameserver ID
 * @property integer $idNameServerSlave1 first slave nameserver ID
 * @property integer $idNameServerSlave2 second slave nameserver ID
 * @property integer $idNameServerSlave3 third slave nameserver ID
 * @property string  $NameServerMasterAlias master nameserver alias host name
 * @property string  $NameServerSlave1Alias first slave nameserver alias host name
 * @property string  $NameServerSlave2Alias second slave nameserver alias host name
 * @property string  $NameServerSlave3Alias third slave nameserver alias host name
 * @property integer $load current load
 */
class NameServerAlias extends CActiveRecord
{
  const PAGESIZE = 10;
  private $_load;

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
      array('idUser,idNameServerMaster,idNameServerSlave1', 'required'),
      array('NameServerMasterAlias,NameServerSlave1Alias', 'required'),
      array('idUser,idNameServerMaster,idNameServerSlave1,idNameServerSlave2,idNameServerSlave3', 'numerical', 'integerOnly' => true),
      array('NameServerMasterAlias,NameServerSlave1Alias,NameServerSlave2Alias,NameServerSlave3Alias', 'length', 'max' => 255),
      array('idNameServerMaster,NameServerMasterAlias', 'safe', 'on' => 'search'),
    );
  }

  public function relations()
  {
    return array(
      'user'             => array(self::BELONGS_TO, 'User', 'idUser'),
      'nameServerMaster' => array(self::BELONGS_TO, 'NameServer', 'idNameServerMaster'),
      'nameServerSlave1' => array(self::BELONGS_TO, 'NameServer', 'idNameServerSlave1'),
      'nameServerSlave2' => array(self::BELONGS_TO, 'NameServer', 'idNameServerSlave2'),
      'nameServerSlave3' => array(self::BELONGS_TO, 'NameServer', 'idNameServerSlave3'),
    );
  }

  public function beforeDelete()
  {
    if (parent::beforeDelete()) {
      if ($this->load) {
        return false;
      }

      return true;
    }
    
    return false;
  }

  public function afterSave()
  {
    parent::afterSave();

    $criteria = new CDbCriteria;
    $criteria->with = array(
      'currentZone',
    );
    $criteria->compare('currentZone.idNameServerAlias', $this->id);
    $domains = Domain::model()->findAll($criteria);

    if (!empty($domains)) {
      foreach ($domains as $domain) {
        $domain->changeNS($this->id, true);
      }
    }
  }

  public function attributeLabels()
  {
    return array(
      'id' => Yii::t('common', 'ID'),
      'idUser' => Yii::t('nameserver', 'Owner'),
      'load' => Yii::t('nameserver', 'Domains'),
      'idNameServerMaster' => Yii::t('nameserver','Master nameserver'),
      'idNameServerSlave1' => Yii::t('nameserver', 'Slave nameserver #1'),
      'idNameServerSlave2' => Yii::t('nameserver', 'Slave nameserver #2'),
      'idNameServerSlave3' => Yii::t('nameserver', 'Slave nameserver #3'),
      'NameServerMasterAlias' => Yii::t('nameserver', 'Master nameserver alias'),
      'NameServerSlave1Alias' => Yii::t('nameserver', 'Slave nameserver #1 alias'),
      'NameServerSlave2Alias' => Yii::t('nameserver', 'Slave nameserver #2 alias'),
      'NameServerSlave3Alias' => Yii::t('nameserver', 'Slave nameserver #3 alias'),
    );
  }

  public function filterByUser($id = null)
  {
    if ($id === null) {
      $id = Yii::app()->user->id;
    }
    
    $this->getDbCriteria()->mergeWith(array(
      'condition' => 't.idUser=:idUser',
      'params' => array(
        ':idUser' => $id,
      ),
    ));
    
    return $this;
  }

  public function search()
  {
    $criteria = new CDbCriteria;
    $criteria->with = array(
      'nameServerMaster',
      'nameServerSlave1',
      'nameServerSlave2',
      'nameServerSlave3',
    );
    $criteria->compare('t.id', $this->id, true);
    $criteria->compare('nameServerMaster.name', $this->idNameServerMaster, true);
    $criteria->compare('t.NameServerMasterAlias', $this->NameServerMasterAlias, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination' => array('pageSize' => self::PAGESIZE),
      'sort' => array(
        'defaultOrder' => 't.id DESC',
      ),
    ));
  }

  public function getNameservers()
  {
    $pk = array();
    $pk[] = $this->idNameServerMaster;
    $pk[] = $this->idNameServerSlave1;
    if ($this->idNameServerSlave2) {
      $pk[] = $this->idNameServerSlave2;
    }
    if ($this->idNameServerSlave3) {
      $pk[] = $this->idNameServerSlave3;
    }

    return NameServer::model()->findAllByPk($pk);
  }

  public function getList()
  {
    $list = array();
    $criteria = $this->getDbCriteria();
    $model = self::model()->findAll($criteria);

    foreach ($model as $alias) {
      $label = $alias->NameServerMasterAlias . ', ' . $alias->NameServerSlave1Alias;
      if (!empty($alias->NameServerSlave2Alias) && !empty($alias->NameServerSlave3Alias)) {
        $label .= ', ' . $alias->NameServerSlave2Alias . ', ' . $alias->NameServerSlave3Alias;
      }
      $list[$alias->id] = $label;
    }
    
    return $list;
  }
  
  public function getLoad()
  {
    if ($this->_load == null) {
      if (!$this->isNewRecord) {
        $criteria = new CDbCriteria;
        $criteria->with = array(
          'currentZone',
        );
        $criteria->compare('currentZone.idNameServerAlias', $this->id);
        $this->_load = Domain::model()->count($criteria);
      }
    }
    
    return $this->_load;
  }
}
