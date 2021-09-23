<?php

class m130105_174055_add_pricing_plans extends CDbMigration
{
  public function safeUp()
  {
    $this->createTable('{{PricingPlan}}', array(
      'id' => 'pk',
      'status' => "TINYINT(1) NOT NULL DEFAULT '1'",
      'type' => "TINYINT(1) NOT NULL DEFAULT '1'",
      'title' => "VARCHAR(255) NOT NULL DEFAULT ''",
      'domainsQty' => "INT NOT NULL DEFAULT 3",
      'usersQty' => "INT NOT NULL DEFAULT 1",
      'nameserversQty' => "INT UNSIGNED NOT NULL DEFAULT 2",
      'minTtl' => "INT UNSIGNED NOT NULL DEFAULT 3600",
      'accessApi' => "TINYINT(1) NOT NULL DEFAULT '0'",
      'pricePerYear' => "DECIMAL(12,2) NOT NULL DEFAULT '0.00'",
      'pricePerMonth' => "DECIMAL(12,2) NOT NULL DEFAULT '0.00'",
      'billing' => "TINYINT(2) NOT NULL DEFAULT '2'",
    ), "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");
    $this->insert('{{PricingPlan}}', array(
      'title' => 'Free account',
      'status' => 1,
      'type' => 0,
      'domainsQty' => 3,
      'usersQty' => 1,
      'nameserversQty' => 2,
      'minTtl' => 3600,
      'accessApi' => 0,
      'billing' => 0,
    ));
    $this->insert('{{PricingPlan}}', array(
      'title' => 'Premium silver',
      'status' => 1,
      'type' => 1,
      'domainsQty' => 100,
      'usersQty' => 1,
      'nameserversQty' => 2,
      'minTtl' => 1,
      'accessApi' => 0,
      'pricePerYear' => 19.95,
      'pricePerMonth' => 1.99,
      'billing' => 3,
    ));
    $this->insert('{{PricingPlan}}', array(
      'title' => 'Premium gold',
      'status' => 1,
      'type' => 1,
      'domainsQty' => 500,
      'usersQty' => 1,
      'nameserversQty' => 2,
      'minTtl' => 1,
      'accessApi' => 0,
      'pricePerYear' => 39.95,
      'pricePerMonth' => 3.99,
      'billing' => 3,
    ));
    $this->insert('{{PricingPlan}}', array(
      'title' => 'Corporate silver',
      'status' => 1,
      'type' => 2,
      'domainsQty' => 1000,
      'usersQty' => 10,
      'nameserversQty' => 4,
      'minTtl' => 1,
      'accessApi' => 1,
      'pricePerYear' => 149.95,
      'pricePerMonth' => 14.99,
      'billing' => 3,
    ));
    $this->insert('{{PricingPlan}}', array(
      'title' => 'Corporate gold',
      'status' => 1,
      'type' => 2,
      'domainsQty' => -1,
      'usersQty' => -1,
      'nameserversQty' => 4,
      'minTtl' => 1,
      'accessApi' => 1,
      'pricePerYear' => 349.95,
      'pricePerMonth' => -1,
      'billing' => 1,
    ));

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
