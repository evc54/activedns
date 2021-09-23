<?php
/**
  Project       : ActiveDNS
  Document      : controllers/StatController.php
  Document type : PHP script file
  Created at    : 20.11.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
  Description   : Statistics receiver controller
*/
class StatController extends CController
{
  public function actionIndex()
  {
    $r = Yii::app()->request;
    $token = $r->getParam('token');
    $address = $r->userHostAddress;
    if ($r->isPostRequest && $token) {
      $criteria = new CDbCriteria;
      $criteria->compare('token', $token);
      $criteria->compare('address', $address);
      $nameserver = NameServer::model()->find($criteria);
      if ($nameserver !== null) {
        // check completed - parse statistics
        $domainStat = array();
        $lastStatUpload = $nameserver->lastStatUpload;
        $currentStatUpload = 0;
        $file = CUploadedFile::getInstanceByName('stat');
        $stat = file_get_contents($file->tempName);
        $stat = explode("\n", $stat);
        foreach ($stat as $line) {
          $chunks = explode(' ',$line);
          if (count($chunks) < 4) continue; // no statistics available
          if ($chunks[0] == 'NSTATS') {
            $time = intval($chunks[2]);
            if ($time > $lastStatUpload) {
              $currentStatUpload = max($time,$currentStatUpload);
              $domain = rtrim($chunks[1], '.');
              $date = gmdate('Y-m-d', $time);
              $hour = gmdate('H', $time);
              if (!isset($domainStat[$domain][$date][$hour])) {
                $domainStat[$domain][$date][$hour] = 0;
              }
              for ($i = 3; $i < count($chunks); $i++) {
                list($statRR, $statValue) = explode('=', $chunks[$i]);
                $domainStat[$domain][$date][$hour] += intval($statValue);
              }
            }
          }
        }

        $domains = array_keys($domainStat);
        $condition = Yii::app()->db->quoteColumnName('idDomain') . '=:idDomain AND '
                    . Yii::app()->db->quoteColumnName('date') . '=:date AND '
                    . Yii::app()->db->quoteColumnName('hour') . '=:hour';
        $sqlHourly = "INSERT INTO {{DomainStat}}(idDomain,date,hour,requests) VALUES(:idDomain,:date,:hour,:requests)" .
                "ON DUPLICATE KEY UPDATE requests = requests + VALUES(requests)";
        $sqlDaily =  "INSERT INTO {{DomainStatDaily}}(idDomain,date,requests) VALUES(:idDomain,:date,:requests)" .
                "ON DUPLICATE KEY UPDATE requests = requests + VALUES(requests)";
        $sqlMonthly = "INSERT INTO {{DomainStatMonthly}}(idDomain,year,month,requests) VALUES(:idDomain,:year,:month,:requests)" .
                "ON DUPLICATE KEY UPDATE requests = requests + VALUES(requests)";
        $statHourly = Yii::app()->db->createCommand($sqlHourly);
        $statDaily = Yii::app()->db->createCommand($sqlDaily);
        $statMonthly = Yii::app()->db->createCommand($sqlMonthly);
        foreach ($domains as $domain) {
          $model = Domain::model()->find("name=:name", array(':name' => $domain));
          if ($model == null) {
            continue;
          }
          foreach ($domainStat[$domain] as $date => $domainHourStat) {
            foreach ($domainHourStat as $hour => $requests) {
              $dateSplitted = explode('-',$date);

              $statHourly->bindValue(':idDomain', $model->id, PDO::PARAM_INT);
              $statHourly->bindValue(':date', $date, PDO::PARAM_STR);
              $statHourly->bindValue(':hour', $hour, PDO::PARAM_INT);
              $statHourly->bindValue(':requests', $requests, PDO::PARAM_INT);
              $statHourly->execute();

              $statDaily->bindValue(':idDomain', $model->id, PDO::PARAM_INT);
              $statDaily->bindValue(':date', $date, PDO::PARAM_STR);
              $statDaily->bindValue(':requests', $requests, PDO::PARAM_INT);
              $statDaily->execute();

              $statMonthly->bindValue(':idDomain', $model->id, PDO::PARAM_INT);
              $statMonthly->bindValue(':year', $dateSplitted[0], PDO::PARAM_INT);
              $statMonthly->bindValue(':month', $dateSplitted[1], PDO::PARAM_INT);
              $statMonthly->bindValue(':requests', $requests, PDO::PARAM_INT);
              $statMonthly->execute();
            }
          }
        }
        if ($currentStatUpload > 0) {
          $nameserver->lastStatUpload = $currentStatUpload;
          $nameserver->save();
        }
        header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0') . ' 202 Accepted', true, 202);
      }
    }
  }
}
