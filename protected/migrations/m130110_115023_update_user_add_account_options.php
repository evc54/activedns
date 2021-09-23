<?php

class m130110_115023_update_user_add_account_options extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{User}}', 'billing', "INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `idPricingPlan`");
    $this->addColumn('{{User}}', 'language', "VARCHAR(16) AFTER `ns4`");
    $this->addColumn('{{User}}', 'currency', "VARCHAR(16) AFTER `language`");
    $this->addColumn('{{User}}', 'dateFormat', "VARCHAR(32) AFTER `currency`");
    $this->addColumn('{{User}}', 'timeFormat', "VARCHAR(32) AFTER `dateFormat`");
    $this->addColumn('{{User}}', 'timeZone', "VARCHAR(64) AFTER `timeFormat`");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
