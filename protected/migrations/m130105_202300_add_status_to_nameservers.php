<?php

class m130105_202300_add_status_to_nameservers extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{NameServer}}', 'status', "TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' AFTER `id`");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
