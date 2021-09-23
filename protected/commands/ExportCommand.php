<?php
/**
  Project       : ActiveDNS
  Document      : ExportCommand.php
  Document type : PHP script file
  Created at    : 04.10.2015
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Export data from the application database
*/
class ExportCommand extends CConsoleCommand
{
  public function run($args)
  {
    if (empty($args)) {
      echo 'Please provide export file name.' . PHP_EOL;
      exit(1);
    }

    $filename = implode('', $args);

    if (file_exists($filename)) {
      echo 'File ' . $filename . ' is exist. Provide another file name.' . PHP_EOL;
      exit(1);
    }

    if (!is_writable(dirname($filename))) {
      echo 'Can not write to the file ' . $filename . '.' . PHP_EOL;
      exit(1);
    }

    echo 'Starting up, data will be out put to the file ' . $filename . PHP_EOL . PHP_EOL;

    $data = array();

    echo 'Collecting users... ';
    foreach (User::model()->findAll() as $user) {
      $data['users'][$user->id] = $user->attributes;
    }
    echo 'OK' . PHP_EOL;

    echo 'Collecting nameservers... ';
    foreach (NameServer::model()->findAll() as $ns) {
      $data['nameservers'][$ns->id] = $ns->attributes;
    }
    echo 'OK' . PHP_EOL;

    echo 'Collecting nameserver aliases... ';
    foreach (NameServerAlias::model()->findAll() as $alias) {
      if (isset($data['users'][$alias->idUser])) {
        $data['users'][$alias->idUser]['ns-aliases'][$alias->id] = $alias->attributes;
      }
    }
    echo 'OK' . PHP_EOL;

    echo 'Collecting zone templates... ';
    foreach (Template::model()->with(array('records'))->findAll() as $tpl) {
      if ($tpl->idUser == 0) {
        $data['templates'][$tpl->id] = $tpl->attributes;
        foreach ($tpl->records as $record) {
          $data['templates'][$tpl->id]['records'][$record->id] = $record->attributes;
        }
      }
      elseif (isset($data['users'][$tpl->idUser])) {
        $data['users'][$tpl->idUser]['zone-templates'][$tpl->id] = $tpl->attributes;
        foreach ($tpl->records as $record) {
          $data['users'][$tpl->idUser]['zone-templates'][$tpl->id]['records'][] = $record->attributes;
        }
      }
    }
    echo 'OK' . PHP_EOL;

    echo 'Collecting alerts... ';
    foreach (Alert::model()->findAll() as $alert) {
      if (isset($data['users'][$alert->idUser])) {
        $data['users'][$alert->idUser]['domain-alerts'][$alert->id] = $alert->attributes;
      }
    }
    echo 'OK' . PHP_EOL;

    echo 'Collecting domains, zones and resource records... ';
    foreach (Domain::model()->with(array('zone', 'zone.record', 'zone.nameservers'))->findAll() as $domain) {
      if (isset($data['users'][$domain->idUser])) {
        $data['users'][$domain->idUser]['domains'][$domain->id] = $domain->attributes;
        foreach ($domain->zone as $zone) {
          $data['users'][$domain->idUser]['domains'][$domain->id]['zones'][$zone->id] = $zone->attributes;
          foreach ($zone->record as $record) {
            $data['users'][$domain->idUser]['domains'][$domain->id]['records'][$record->idZone][$record->id] = $record->attributes;
          }
          foreach ($zone->nameservers as $ns) {
            $data['users'][$domain->idUser]['domains'][$domain->id]['zones'][$zone->id]['nameservers'][$ns->id] = true;
          }
        }
      }
    }
    echo 'OK' . PHP_EOL;

    echo 'Dumping data to the file ' . $filename . '... ';
    file_put_contents($filename, CJSON::encode($data));
    echo ' OK, we have done.' . PHP_EOL . PHP_EOL;
  }
}
