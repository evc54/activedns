<?php

class m130110_134822_change_email extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{ChangeEmail}}', array(
      'id' => "pk",
      'userID' => "INT(11) UNSIGNED DEFAULT '0'",
      'email' => "VARCHAR(255) NOT NULL DEFAULT ''",
      'newEmail' => "VARCHAR(255) NOT NULL DEFAULT ''",
      'activeBefore' => "INT(11) UNSIGNED DEFAULT '0'",
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');

    return true;
  }

  public function down()
  {
    return false;
  }
}
