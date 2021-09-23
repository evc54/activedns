<?php

class m130526_193945_domain_add_allow_auto_check extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{Domain}}', 'allowAutoCheck', "TINYINT(1) DEFAULT 1 AFTER `lastAutoCheck`");

    return true;
  }

  public function down()
  {
    return false;
  }
}
