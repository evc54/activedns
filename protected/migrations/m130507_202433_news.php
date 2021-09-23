<?php

class m130507_202433_news extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{News}}', array(
      'id' => "pk",
      'idUser' => 'INT(11) NOT NULL DEFAULT 0',
      'public' => "TINYINT(1) NOT NULL DEFAULT 0",
      'create' => 'INT(11) NOT NULL DEFAULT 0',
      'update' => 'INT(11) NOT NULL DEFAULT 0',
      'publish' => 'INT(11) NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');

    $this->createTable('{{NewsContent}}', array(
      'id' => "pk",
      'idNews' => 'INT(11) NOT NULL DEFAULT 0',
      'language' => 'VARCHAR(2)',
      'title' => "VARCHAR(255)",
      'announce' => "VARCHAR(1022)",
      'fulltext' => "TEXT",
      'concat' => "TINYINT(1) NOT NULL DEFAULT 0",
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');

    $this->createIndex('idxNewsID', '{{NewsContent}}', 'idNews');

    return true;
  }

  public function down()
  {
    return false;
  }
}
