<?php

class m130529_105725_ns_alias_refactoring_queries extends CDbMigration
{
  public function safeUp()
  {
    $this->createTable('{{ZoneNameServer}}', array(
      'zoneID' => "INT(11)",
      'nameServerID' => "INT(11)",
      'PRIMARY KEY(`zoneID`,`nameServerID`)',
    ), "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
    $this->execute('INSERT INTO {{ZoneNameServer}}(zoneID,nameServerID) SELECT d.idZoneCurrent,s.idNameServer FROM {{Domain}} d,{{DomainNameServer}} s WHERE d.id = s.idDomain');
    $this->execute('INSERT INTO {{ZoneNameServer}}(zoneID,nameServerID) SELECT z.id,s.idNameServer FROM {{Zone}} z,{{Domain}} d,{{DomainNameServer}} s WHERE d.id = s.idDomain AND z.idDomain = d.id AND z.serial = 0');
    $this->dropTable('{{DomainNameServer}}');
    $this->execute('DELETE FROM {{Zone}} WHERE id NOT IN (SELECT idZoneCurrent FROM {{Domain}}) AND serial <> 0');

    $this->addColumn('{{Zone}}', 'idNameServerAlias', "INT NOT NULL DEFAULT 0 AFTER `idDomain`");
    $this->execute('UPDATE {{Zone}} z,{{Domain}} d SET d.idZoneReplicated = 0, z.idNameServerAlias = d.idNameServerAlias WHERE z.idDomain = d.id');
    $this->dropColumn('{{Domain}}','idNameServerAlias');

    return true;
  }

  public function safeDown()
  {
    return false;
  }
}
