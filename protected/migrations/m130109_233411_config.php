<?php

class m130109_233411_config extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{Config}}', array(
      'id' => "VARCHAR(63) NOT NULL PRIMARY KEY",
      'value' => "VARCHAR(1022) NOT NULL DEFAULT ''",
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->insert('{{Config}}', array('id' => 'AllowSignup', 'value' => '1'));
    $this->insert('{{Config}}', array('id' => 'PrimaryLanguage', 'value' => 'en'));
    $this->insert('{{Config}}', array('id' => 'PrimaryCurrency', 'value' => 'USD'));
    $this->insert('{{Config}}', array('id' => 'NewAccountPlan', 'value' => '1'));
    return true;
  }

  public function down()
  {
    return false;
  }
}
