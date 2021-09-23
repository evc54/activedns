<?php

class m130711_143845_nameserver_add_public_address extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{NameServer}}', 'publicAddress', 'VARCHAR(1024) AFTER `address`');

    return true;
  }

  public function down()
  {
    return false;
  }
}
