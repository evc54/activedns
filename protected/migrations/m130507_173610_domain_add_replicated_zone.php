<?php

class m130507_173610_domain_add_replicated_zone extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{Domain}}', 'idZoneReplicated', "INT NOT NULL DEFAULT 0 AFTER `idZoneCurrent`");

    return true;
  }

  public function down()
  {
    return false;
  }
}
