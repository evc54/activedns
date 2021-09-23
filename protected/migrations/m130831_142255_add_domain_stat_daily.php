<?php

class m130831_142255_add_domain_stat_daily extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{DomainStatDaily}}', array(
      'idDomain' => 'int',
      'date' => 'date',
      'requests' => 'int',
      'PRIMARY KEY(`idDomain`,`date`)',
    ), 'ENGINE=InnoDB');

    $this->createIndex('idxDomainID', '{{DomainStatDaily}}', 'idDomain');
    $this->createIndex('idxDate', '{{DomainStatDaily}}', 'date');

    $this->execute("INSERT INTO {{DomainStatDaily}} SELECT `idDomain`,`date`,SUM(requests) FROM {{DomainStat}} GROUP BY `idDomain`,`date`");

    return true;
  }

  public function down()
  {
    return false;
  }
}
