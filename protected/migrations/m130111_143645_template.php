<?php

class m130111_143645_template extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{Template}}', 'type', "INT(11) NOT NULL DEFAULT 1");
    $this->addColumn('{{Template}}', 'name', "VARCHAR(255) NOT NULL DEFAULT ''");
    $this->addColumn('{{Template}}', 'created', "INT(11) NOT NULL DEFAULT 0");
    $this->addColumn('{{Template}}', 'updated', "INT(11) NOT NULL DEFAULT 0");

    $this->createTable('{{TemplateRecord}}', array(
      'id' => "pk",
      'templateID' => 'INT(11) NOT NULL DEFAULT 0',
      'host' => "VARCHAR(63) NOT NULL DEFAULT ''",
      'class' => "VARCHAR(2) NOT NULL DEFAULT ''",
      'type' => "VARCHAR(15) NOT NULL DEFAULT ''",
      'rdata' => "VARCHAR(163) NOT NULL DEFAULT ''",
      'ttl' => 'INT(10) NOT NULL DEFAULT 0',
      'priority' => 'INT(10) NOT NULL DEFAULT 0',
      'proto' => "VARCHAR(15) NOT NULL DEFAULT ''",
      'name' => "VARCHAR(63) NOT NULL DEFAULT ''",
      'weight' => 'INT(10) NOT NULL DEFAULT 0',
      'port' => 'INT(10) NOT NULL DEFAULT 0',
      'target' => "VARCHAR(63) NOT NULL DEFAULT ''",
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');

    $this->createIndex('idxTemplateID', '{{TemplateRecord}}', 'templateID');

    return true;
  }

  public function down()
  {
    return false;
  }
}
