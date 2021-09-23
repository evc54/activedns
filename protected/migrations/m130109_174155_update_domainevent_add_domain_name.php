<?php

class m130109_174155_update_domainevent_add_domain_name extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{DomainEvent}}', 'name', "VARCHAR(255)");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
