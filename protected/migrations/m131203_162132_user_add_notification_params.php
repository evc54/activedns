<?php

class m131203_162132_user_add_notification_params extends CDbMigration
{
  public function up()
  {
    $this->renameColumn('{{User}}', 'lastNotifyTime', 'lastEventNotifyTime');
    $this->addColumn('{{User}}', 'expireNotify', 'int default 0');
    $this->addColumn('{{User}}', 'lastExpireNotifyTime', 'int default 0');
    $this->addColumn('{{User}}', 'alertNotify', 'int default 0');
    $this->addColumn('{{User}}', 'lastAlertNotifyTime', 'int default 0');
    $this->addColumn('{{Alert}}', 'notified', 'tinyint(1) default 0');

    return true;
  }

  public function down()
  {
    return false;
  }
}
