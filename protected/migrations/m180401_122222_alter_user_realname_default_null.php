<?php

class m180401_122222_alter_user_realname_default_null extends CDbMigration
{
  public function up()
  {
    $this->alterColumn('{{User}}', 'realname', "varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''");

    return true;
  }

  public function down()
  {
    return false;
  }
}
