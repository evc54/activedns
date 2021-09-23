<?php

class m130105_150155_remove_info extends CDbMigration
{
  public function safeUp()
  {
    $this->dropTable('{{Info}}');

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
