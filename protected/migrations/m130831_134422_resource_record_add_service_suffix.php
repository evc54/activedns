<?php

class m130831_134422_resource_record_add_service_suffix extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{ResourceRecord}}', 'suffix', 'VARCHAR(64) AFTER `proto`');

    return true;
  }

  public function down()
  {
    return false;
  }
}
