<?php

class m120101_000001_base extends CDbMigration
{
  public function up()
  {
    $this->createTable('{{Alert}}', array(
      'id' => 'pk',
      'idUser' => 'int(10) unsigned NOT NULL',
      'idDomain' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'type' => "int(10) unsigned NOT NULL DEFAULT 0",
      'create' => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
      'alert' => "varchar(1022) COLLATE utf8_unicode_ci NOT NULL",
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('AlertUserIdx', '{{Alert}}', 'idUser');

    $this->createTable('{{Domain}}', array(
      'id' => 'pk',
      'idUser' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'idZoneCurrent' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'status' => 'tinyint(3) unsigned NOT NULL DEFAULT 0',
      'tld' => 'varchar(63) NOT NULL',
      'name' => 'varchar(127) NOT NULL',
      'create' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'update' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'lastAutoCheck' => 'int(10) unsigned NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('DomainUserIdx', '{{Domain}}', 'idUser');

    $this->createTable('{{Zone}}', array(
      'id' => 'pk',
      'idDomain' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'hostmaster' => 'varchar(64) NOT NULL',
      'create' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'serial' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'refresh' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'retry' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'expire' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'minimum' => 'int(10) unsigned NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('ZoneDomainIdx', '{{Zone}}', 'idDomain');

    $this->createTable('{{ResourceRecord}}', array(
      'id' => 'pk',
      'idZone' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'host' => 'varchar(63) NOT NULL',
      'class' => 'varchar(2) NOT NULL',
      'type' => 'varchar(15) NOT NULL',
      'rdata' => "varchar(163) NOT NULL DEFAULT ''",
      'ttl' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'priority' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'proto' => 'varchar(15) NOT NULL',
      'name' => 'varchar(63) NOT NULL',
      'weight' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'port' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'target' => 'varchar(63) NOT NULL',
      'readonly' => 'tinyint(1) NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('ResourceRecordZoneIdx', '{{ResourceRecord}}', 'idZone');

    $this->createTable('{{DomainEvent}}', array(
      'id' => 'pk',
      'idUser' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'idDomain' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'idEventType' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'event' => 'varchar(1022) NOT NULL',
      'create' => 'int(10) unsigned NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('DomainEventDomainUserIdx', '{{DomainEvent}}', 'idUser, idDomain');

    $this->createTable('{{DomainNameServer}}', array(
      'idDomain' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'idNameServer' => 'int(10) unsigned NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('DomainNameServerDomainNameServerIdx', '{{DomainNameServer}}', 'idDomain, idNameServer');

    $this->createTable('{{DomainStat}}', array(
      'idDomain' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'date' => "date NOT NULL DEFAULT '0000-00-00'",
      'hour' => 'int(4) unsigned NOT NULL DEFAULT 0',
      'requests' => 'int(10) unsigned NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->addPrimaryKey('DomainStatPk', '{{DomainStat}}', 'idDomain, date, hour');
    $this->createIndex('DomainStatDomainIdx', '{{DomainStat}}', 'idDomain');
    $this->createIndex('DomainStatDateIdx', '{{DomainStat}}', 'date');

    $this->createTable('{{Info}}', array(
      'id' => 'pk',
      'idDomain' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'ns1' => 'varchar(127) NOT NULL',
      'ns2' => 'varchar(127) NOT NULL',
      'ns3' => 'varchar(127) NOT NULL',
      'ns4' => 'varchar(127) NOT NULL',
      'create' => "date NOT NULL DEFAULT '0000-00-00'",
      'update' => "date NOT NULL DEFAULT '0000-00-00'",
      'expire' => "date NOT NULL DEFAULT '0000-00-00'",
      'registrar' => 'varchar(255) NOT NULL',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('InfoDomainIdx', '{{Info}}', 'idDomain');

    $this->createTable('{{NameServer}}', array(
      'id' => 'pk',
      'name' => 'varchar(64) NOT NULL',
      'address' => "varchar(15) NOT NULL DEFAULT ''",
      'type' => 'tinyint(3) unsigned NOT NULL DEFAULT 1',
      'idNameServerPair' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'token' => 'varchar(32) NOT NULL',
      'lastStatUpload' => 'int(10) unsigned NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->insert('{{NameServer}}', array(
      'id' => 1,
      'name' => 'ns1.activedns.net',
      'address' => "127.0.0.1",
      'type' => 1,
      'idNameServerPair' => 2,
      'token' => '0123456789',
    ));
    $this->insert('{{NameServer}}', array(
      'id' => 2,
      'name' => 'ns2.activedns.net',
      'address' => "127.0.0.2",
      'type' => 2,
      'idNameServerPair' => 1,
      'token' => '9876543210',
    ));

    $this->createTable('{{RestoreAccess}}', array(
      'id' => 'pk',
      'timestamp' => 'int(10) unsigned NOT NULL',
      'email' => 'varchar(64) NOT NULL',
      'activeBefore' => "timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'",
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');

    $this->createTable('{{Template}}', array(
      'id' => 'pk',
      'idUser' => 'int(10) unsigned NOT NULL',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->createIndex('TemplateUserIdx', '{{Template}}', 'idUser');

    $this->createTable('{{User}}', array(
      'id' => 'pk',
      'create' => 'int(10) unsigned NOT NULL',
      'status' => 'tinyint(3) unsigned NOT NULL DEFAULT 1',
      'role' => 'tinyint(3) unsigned NOT NULL DEFAULT 0',
      'realname' => 'varchar(255) NOT NULL',
      'email' => 'varchar(64) NOT NULL',
      'password' => 'varchar(32) NOT NULL',
      'level' => 'tinyint(3) unsigned NOT NULL DEFAULT 0',
      'paidTill' => "date NOT NULL DEFAULT '0000-00-00'",
      'ns1' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'ns2' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'ns3' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'ns4' => 'int(10) unsigned NOT NULL DEFAULT 0',
      'lastEventSeen' => 'int(10) unsigned NOT NULL DEFAULT 0',
    ), 'ENGINE=InnoDB,DEFAULT CHARACTER SET=utf8,COLLATE=utf8_unicode_ci');
    $this->insert('{{User}}', array(
      'create' => time(),
      'status' => 1,
      'role' => 1,
      'realname' => 'Site administrator',
      'email' => 'admin@activedns.net',
      'password' => md5('ad246z' . '123456'),
      'level' => 5,
      'ns1' => 1,
      'ns2' => 2,
    ));

    return true;
  }

  public function down()
  {
    return false;
  }
}
