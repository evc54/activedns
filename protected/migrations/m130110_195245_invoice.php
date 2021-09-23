<?php

class m130110_195245_invoice extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{Invoice}}', array(
      'id' => "pk",
      'status' => 'INT(11) NOT NULL DEFAULT 0',
      'userID' => 'INT(11) NOT NULL DEFAULT 0',
      'invoiceID' => "VARCHAR(32) NOT NULL DEFAULT ''",
      'transactionID' => "VARCHAR(32) NOT NULL DEFAULT ''",
      'amount' => "NUMERIC(25,2) DEFAULT 0",
      'incomingAmount' => "NUMERIC(25,2) DEFAULT 0",
      'signature' => "VARCHAR(32) NOT NULL DEFAULT ''",
      'incomingSignature' => "VARCHAR(32) NOT NULL DEFAULT ''",
      'created' => 'INT(11) NOT NULL DEFAULT 0',
      'completed' => 'INT(11) NOT NULL DEFAULT 0',
      'planID' => 'INT(11) NOT NULL DEFAULT 0',
      'billing' => 'INT(11) NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');

    return true;
  }

  public function down()
  {
    return false;
  }
}
