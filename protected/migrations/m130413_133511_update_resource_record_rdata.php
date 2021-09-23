<?php

class m130413_133511_update_resource_record_rdata extends CDbMigration
{
  public function safeUp()
  {
    $this->alterColumn('{{ResourceRecord}}', 'rdata', "VARCHAR(4096)");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
