<?php

class m130109_122805_add_support extends CDbMigration
{
  public function safeUp()
  {
    $this->createTable('{{SupportTicket}}', array(
      'id' => 'pk',
      'status' => "TINYINT(2) NOT NULL DEFAULT '1'",
      'subject' => "VARCHAR(255) NOT NULL DEFAULT ''",
      'authorID' => "INT(11) NOT NULL DEFAULT 0",
      'created' => "INT(11) NOT NULL DEFAULT 0",
      'replied' => "INT(11) NOT NULL DEFAULT 0",
    ), "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

    $this->createTable('{{SupportTicketReply}}', array(
      'id' => 'pk',
      'ticketID'=>"INT(11) NOT NULL DEFAULT 0",
      'authorID' => "INT(11) NOT NULL DEFAULT 0",
      'text' => "TEXT",
      'created' => "INT(11) NOT NULL DEFAULT 0",
    ), "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
