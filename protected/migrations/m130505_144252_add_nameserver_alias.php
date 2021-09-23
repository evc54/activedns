<?php

class m130505_144252_add_nameserver_alias extends CDbMigration
{
  public function safeUp()
  {
    $this->createTable('{{NameServerAlias}}', array(
      'id' => 'pk',
      'idUser' => 'INT',
      'idNameServerMaster' => 'INT',
      'idNameServerSlave1' => 'INT',
      'idNameServerSlave2' => 'INT',
      'idNameServerSlave3' => 'INT',
      'NameServerMasterAlias' => 'VARCHAR(255)',
      'NameServerSlave1Alias' => 'VARCHAR(255)',
      'NameServerSlave2Alias' => 'VARCHAR(255)',
      'NameServerSlave3Alias' => 'VARCHAR(255)',
    ), "ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
