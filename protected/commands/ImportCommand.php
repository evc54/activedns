<?php
/**
  Project       : ActiveDNS
  Document      : ImportCommand.php
  Document type : PHP script file
  Created at    : 07.10.2015
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Import data from json file
*/
class ImportCommand extends CConsoleCommand
{
  public function run($args)
  {
    if (empty($args)) {
      echo 'Please provide import file name.' . PHP_EOL;
      exit(1);
    }

    $filename = implode('', $args);

    if (!file_exists($filename)) {
      echo 'File ' . $filename . ' is not exist. Provide another file name.' . PHP_EOL;
      exit(1);
    }

    if (!is_readable($filename)) {
      echo 'Can not read from the file ' . $filename . '.' . PHP_EOL;
      exit(1);
    }

    echo 'Starting up, data will be get from the file ' . $filename . PHP_EOL . PHP_EOL;

    $data = file_get_contents($filename);
    $data = CJSON::decode($data);
    if (empty($data)) {
      echo 'Empty data file. Can not continue.' . PHP_EOL;
      exit(1);
    }

    $map = array(
      'users'              => array(),
      'domains'            => array(),
      'zones'              => array(),
      'ns-aliases'         => array(),
      'domain-alerts'      => array(),
      'zone-templates'     => array(),
      'nameserver-aliases' => array(),
    );

    $counters = array(
      'users'     => 0,
      'domains'   => 0,
      'zones'     => 0,
      'templates' => isset($data['templates']) && is_array($data['templates']) ? count($data['templates']) : 0,
      'alerts'    => isset($data['alerts']) && is_array($data['alerts']) ? count($data['alerts']) : 0,
    );

    echo 'Start checking integrity of data.' . PHP_EOL;

    $errors = array();
    $warnings = array();
    foreach ($data['users'] as $record) {
      $user = User::model()->findByAttributes(array('email' => $record['email']));
      if ($user != null) {
        $warnings[] = 'Account with e-mail address ' . $user->email . ' is already registered.';
        $map['users'][$record['id']] = $user->id;
      }

      $counters['users']++;

      if (isset($record['domains'])) {
        foreach ($record['domains'] as $domainRecord) {
          $counters['domains']++;
          $domain = Domain::model()->findByAttributes(array('name' => $domainRecord['name']));

          if ($domain != null) {
            $errors[] = 'Domain ' . $domain->name . ' is already registered.';
          }
          $counters['zones'] += isset($domainRecord['zones']) && is_array($domainRecord['zones']) ? count($domainRecord['zones']) : 0;
        }
      }

      $counters['templates'] += isset($record['zone-templates']) && is_array($record['zone-templates']) ? count($record['zone-templates']) : 0;
    }

    if (!empty($errors)) {
      echo 'An errors has been occurred: ' . PHP_EOL;
      foreach ($errors as $message) {
        echo ' * ' . $message . PHP_EOL;
      }
      echo 'Stopped abnormally.' . PHP_EOL . PHP_EOL;
      exit(1);
    }

    echo 'Checking completed. ';
    if (!empty($warnings)) {
      echo 'A warnings has been found: ' . PHP_EOL;
      foreach ($warnings as $message) {
        echo ' * ' . $message . PHP_EOL;
      }
    }
    else {
      echo 'No errors or warnings discovered.' . PHP_EOL;
    }

    echo 'Found ' . $counters['users'] . ' users, ' .
      $counters['domains'] . ' domains, ' .
      $counters['zones'] . ' zones, ' .
      $counters['templates'] . ' templates' . PHP_EOL . PHP_EOL;

    if ($this->prompt('Are you sure you want to continue (yes/no) ?','no') != 'yes') {
      echo 'Aborted.' . PHP_EOL . PHP_EOL;
      exit(1);
    }

    $counter = 0;
    $total = count($data['users']);
    echo PHP_EOL;
    foreach ($data['users'] as $record) {
      if (!isset($map['users'][$record['id']])) {
        $model = new User();
        $model->attributes = $record;
        $model->id = null;
        $model->save(false);
        $map['users'][$record['id']] = $model->id;
      }

      if (isset($record['ns-aliases']) && is_array($record['ns-aliases'])) {
        foreach ($record['ns-aliases'] as $alias) {
          $model = new NameServerAlias();
          $model->attributes = $alias;
          $model->id = null;
          $model->idUser = $map['users'][$alias['idUser']];
          $model->idNameServerMaster = 3;
          $model->idNameServerSlave1 = 4;
          $model->save(false);
          $map['nameserver-aliases'][$alias['id']] = $model->id;
        }
      }

      if (isset($record['zone-templates']) && is_array($record['zone-templates'])) {
        foreach ($record['zone-templates'] as $tpl) {
          $template = new Template();
          $template->attributes = $tpl;
          $template->id = null;
          $template->idUser = $template->idUser && isset($map['users'][$template->idUser]) ? $map['users'][$template->idUser] : 0;
          $template->save(false);

          if (isset($tpl['records']) && is_array($tpl['records'])) {
            foreach ($tpl['records'] as $rr) {
              $resource = new TemplateRecord();
              $resource->attributes = $rr;
              $resource->id = null;
              $resource->templateID = $template->id;
              $resource->save(false);
            }
          }
        }
      }

      if (isset($record['domains']) && is_array($record['domains'])) {
        foreach ($record['domains'] as $domain) {
          $model = new Domain();
          $model->attributes = $domain;
          $model->id = null;
          $model->idUser = $map['users'][$domain['idUser']];
          $model->save(false);

          $map['domain'][$domain['id']] = $model->id;

          if (isset($domain['zones']) && is_array($domain['zones'])) {
            foreach ($domain['zones'] as $z) {
              $zone = new Zone();
              $zone->attributes = $z;
              $zone->id = null;
              $zone->idDomain = $map['domain'][$zone->idDomain];
              if ($zone->idNameServerAlias) {
                $zone->idNameServerAlias = isset($map['nameserver-aliases'][$zone->idNameServerAlias]) ? $map['nameserver-aliases'][$zone->idNameServerAlias] : 0;
              }
              $zone->save(false);
              $map['zones'][$z['id']] = $zone->id;

              $ns = new ZoneNameServer();
              $ns->zoneID = $zone->id;
              $ns->nameServerID = 3;
              $ns->save(false);

              $ns = new ZoneNameServer();
              $ns->zoneID = $zone->id;
              $ns->nameServerID = 4;
              $ns->save(false);
            }
          }

          if (isset($domain['records']) && is_array($domain['records'])) {
            foreach ($domain['records'] as $zrr) {
              if (is_array($zrr) && count($zrr)) {
                foreach ($zrr as $rr) {
                  $resource = new ResourceRecord();
                  $resource->attributes = $rr;
                  $resource->id = null;
                  if (!isset($map['zones'][$rr['idZone']])) {
                    continue;
                  }
                  $resource->idZone = $map['zones'][$rr['idZone']];
                  if ($resource->type == 'NS' && $resource->rdata == 'ns1.activedns.ru.') {
                    $resource->rdata = 'ns13.activedns.net.';
                  }
                  if ($resource->type == 'NS' && $resource->rdata == 'ns2.activedns.ru.') {
                    $resource->rdata = 'ns14.activedns.net.';
                  }
                  if ($resource->type == 'NS' && ($resource->rdata == 'ns3.activedns.ru.' || $resource->rdata == 'ns4.activedns.ru.')) {
                    continue;
                  }
                  $resource->save(false);
                }
              }
            }
          }

          $model->idZoneCurrent = isset($map['zones'][$model->idZoneCurrent]) ? $map['zones'][$model->idZoneCurrent] : 0;
          $model->idZoneReplicated = isset($map['zones'][$model->idZoneReplicated]) ? $map['zones'][$model->idZoneReplicated] : 0;
          $model->save(false);
        }
      }
      $counter++;
      $this->progress('Import data in progress, %d%% completed...',$total,$counter);
    }

    echo PHP_EOL . 'OK, we have done.' . PHP_EOL . PHP_EOL;
  }

  protected function progress($message, $total, $current)
  {
    $percent = $total > 0 ? round($current/$total*100) : 100;
    echo "\r" . sprintf($message, $percent);
  }
}
