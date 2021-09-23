<?php

class m130204_220312_domain_event_add_param extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{DomainEvent}}', 'param', "VARCHAR(1022) NOT NULL DEFAULT '' AFTER `event`");

    return true;
  }

  public function down()
  {
    return false;
  }
}
