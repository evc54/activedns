<?php

class m180325_184925_set_default_values_to_resource_record extends CDbMigration
{
  public function up()
  {
    $this->alterColumn('{{ResourceRecord}}', 'proto', "varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''");
    $this->alterColumn('{{ResourceRecord}}', 'suffix', "varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''");
    $this->alterColumn('{{ResourceRecord}}', 'name', "varchar(63) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''");
    $this->alterColumn('{{ResourceRecord}}', 'target', "varchar(63) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''");

    return true;
  }

  public function down()
  {
    return false;
  }
}
