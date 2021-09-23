<?php

class m130831_142255_add_domain_stat_monthly extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{DomainStatMonthly}}', array(
      'idDomain' => 'int',
      'year' => 'smallint',
      'month' => 'tinyint',
      'requests' => 'int',
      'PRIMARY KEY(`idDomain`,`year`,`month`)',
    ), 'ENGINE=InnoDB');

    $this->createIndex('idxDomainID', '{{DomainStatMonthly}}', 'idDomain');
    $this->createIndex('idxYear', '{{DomainStatMonthly}}', 'year');

    $this->execute("INSERT INTO {{DomainStatMonthly}} SELECT `idDomain`,YEAR(date),MONTH(date),SUM(requests) FROM {{DomainStat}} GROUP BY `idDomain`,YEAR(date),MONTH(date)");

    return true;
  }

  public function down()
  {
    return false;
  }
}
