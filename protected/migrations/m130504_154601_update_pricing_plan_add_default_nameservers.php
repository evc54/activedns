<?php

class m130504_154601_update_pricing_plan_add_default_nameservers extends CDbMigration
{
  public function safeUp()
  {
    $this->addColumn('{{PricingPlan}}', 'defaultNameserverMaster', "INT UNSIGNED DEFAULT 0");
    $this->addColumn('{{PricingPlan}}', 'defaultNameserverSlave1', "INT UNSIGNED DEFAULT 0");
    $this->addColumn('{{PricingPlan}}', 'defaultNameserverSlave2', "INT UNSIGNED DEFAULT 0");
    $this->addColumn('{{PricingPlan}}', 'defaultNameserverSlave3', "INT UNSIGNED DEFAULT 0");

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
