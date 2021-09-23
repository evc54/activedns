<?php

class m130704_223641_nameserver_update_address extends CDbMigration
{
  public function up()
  {
    $this->alterColumn('{{NameServer}}', 'address', 'VARCHAR(1024)');

    return true;
  }

  public function down()
  {
    return false;
  }
}
