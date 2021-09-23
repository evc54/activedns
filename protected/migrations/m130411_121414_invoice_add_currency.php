<?php

class m130411_121414_invoice_add_currency extends CDbMigration
{
  public function up()
  {
    $this->addColumn('{{Invoice}}', 'currency', "VARCHAR(255) AFTER `amount`");

    return true;
  }

  public function down()
  {
    return false;
  }
}
