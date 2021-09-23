<?php

class m130902_122234_template_record_add_service_suffix extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{TemplateRecord}}', 'suffix', 'VARCHAR(64) AFTER `proto`');

    return true;
  }

  public function down()
  {
    return false;
  }
}
