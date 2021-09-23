<?php

class m180325_134301_all_dates_set_default_null extends CDbMigration
{
  public function up()
  {
    $this->execute("SET sql_mode = '';");

    $this->alterColumn('{{User}}', 'paidTill', 'date null default null');
    $this->execute("UPDATE {{User}} SET `paidTill` = NULL WHERE `paidTill` = '0000-00-00'");

    $this->alterColumn('{{Domain}}', 'register', 'date null default null');
    $this->execute("UPDATE {{Domain}} SET `register` = NULL WHERE `register` = '0000-00-00'");

    $this->alterColumn('{{Domain}}', 'renewal', 'date null default null');
    $this->execute("UPDATE {{Domain}} SET `renewal` = NULL WHERE `renewal` = '0000-00-00'");

    $this->alterColumn('{{Domain}}', 'expire', 'date null default null');
    $this->execute("UPDATE {{Domain}} SET `expire` = NULL WHERE `expire` = '0000-00-00'");

    return true;
  }

  public function down()
  {
    return false;
  }
}
