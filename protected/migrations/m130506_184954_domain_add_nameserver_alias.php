<?php

class m130506_184954_domain_add_nameserver_alias extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{Domain}}', 'idNameServerAlias', "INT NOT NULL DEFAULT 0 AFTER `idUser`");

    return true;
  }

  public function down()
  {
    return false;
  }
}
