<?php

class m130109_122732_update_user_add_lastsupportseen extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{User}}', 'lastSupportSeen', "INT(11) UNSIGNED NOT NULL DEFAULT '0'");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
