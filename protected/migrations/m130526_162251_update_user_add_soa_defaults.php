<?php

class m130526_162251_update_user_add_soa_defaults extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{User}}', 'soaHostmaster', "VARCHAR(64) AFTER `ns4`");
    $this->addColumn('{{User}}', 'soaRefresh', "INT(10) UNSIGNED DEFAULT 28800 AFTER `soaHostmaster`");
    $this->addColumn('{{User}}', 'soaRetry', "INT(10) UNSIGNED DEFAULT 7200 AFTER `soaRefresh`");
    $this->addColumn('{{User}}', 'soaExpire', "INT(10) UNSIGNED DEFAULT 3628800 AFTER `soaRetry`");
    $this->addColumn('{{User}}', 'soaMinimum', "INT(10) UNSIGNED DEFAULT 3600 AFTER `soaExpire`");

    $this->execute("UPDATE {{User}} SET soaHostmaster = CONCAT(REPLACE(email,'@','.'),'.')");
    
    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
