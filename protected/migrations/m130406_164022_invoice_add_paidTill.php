<?php

class m130406_164022_invoice_add_paidTill extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{Invoice}}', 'paidTill', "DATE NOT NULL DEFAULT '0000-00-00'");

    return true;
  }

  public function down()
  {
    return false;
  }
}
