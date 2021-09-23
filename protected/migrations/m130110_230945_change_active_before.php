<?php

class m130110_230945_change_active_before extends CDbMigration
{
  public function up()
  {
    $this->alterColumn('{{RestoreAccess}}', 'activeBefore', "INT(11) UNSIGNED DEFAULT '0'");

    return true;
  }

  public function down()
  {
    return false;
  }
}
