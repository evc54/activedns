<?php

class m130105_181922_update_user_level_to_plan_id extends CDbMigration
{
  public function safeUp()
  {
    $this->renameColumn('{{User}}', 'level', 'idPricingPlan');

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
