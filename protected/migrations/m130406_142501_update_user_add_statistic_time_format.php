<?php

class m130406_142501_update_user_add_statistic_time_format extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{User}}', 'statisticTimeFormat', "VARCHAR(32) AFTER `timeFormat`");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
