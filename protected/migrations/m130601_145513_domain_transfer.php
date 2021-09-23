<?php

class m130601_145513_domain_transfer extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{DomainTransfer}}', array(
      'id' => "pk",
      'domainID' => 'INT(11) NOT NULL DEFAULT 0',
      'address' => 'VARCHAR(32)',
      'allowNotify' => "TINYINT(1) NOT NULL DEFAULT 1",
      'allowTransfer' => "TINYINT(1) NOT NULL DEFAULT 1",
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');

    return true;
  }

  public function down()
  {
    return false;
  }
}
