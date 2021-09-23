<?php

class m130111_124401_invoice_add_email extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{Invoice}}', 'email', "VARCHAR(255) NOT NULL DEFAULT '' AFTER userID");

    return true;
  }

  public function down()
  {
    return false;
  }
}
