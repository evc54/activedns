<?php

class m130413_130002_update_user_add_notifier_sent_time extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{User}}', 'lastNotifyTime', "INT UNSIGNED DEFAULT 0");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
