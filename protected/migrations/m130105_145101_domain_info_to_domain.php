<?php

class m130105_145101_domain_info_to_domain extends CDbMigration
{

  public function safeUp()
  {
    $this->alterColumn('{{Domain}}', 'name', 'varchar(255)');
    $this->addColumn('{{Domain}}', 'ns1', 'varchar(127)');
    $this->addColumn('{{Domain}}', 'ns2', 'varchar(127)');
    $this->addColumn('{{Domain}}', 'ns3', 'varchar(127)');
    $this->addColumn('{{Domain}}', 'ns4', 'varchar(127)');
    $this->addColumn('{{Domain}}', 'register', "date not null default '0000-00-00'");
    $this->addColumn('{{Domain}}', 'renewal', "date not null default '0000-00-00'");
    $this->addColumn('{{Domain}}', 'expire', "date not null default '0000-00-00'");
    $this->addColumn('{{Domain}}', 'registrar', 'varchar(255)');

    $this->execute('UPDATE {{Domain}} d,{{Info}} i SET d.ns1=i.ns1,d.ns2=i.ns2,d.ns3=i.ns3,d.ns4=i.ns4,d.register=i.create,d.renewal=i.update,d.expire=i.expire,d.registrar=i.registrar WHERE d.id=i.idDomain');
    $this->execute("UPDATE {{Domain}} SET name = CONCAT(name,'.',tld)");

    $this->dropColumn('{{Domain}}', 'tld');

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
