<?php
/**
    Project       : ActiveDNS
    Document      : NotifierCommand.php
    Document type : PHP script file
    Created at    : 14.01.2013
    Author        : Eugene V Chernyshev <evc22rus@gmail.com>
    Description   : Notifies users by email about events and alerts
*/
class NotifierCommand extends CConsoleCommand
{
  const EVENT_NOTIFY = 86400; // 1 day
  const EXPIRE_NOTIFY = 86400; // 1 day
  const ALERT_NOTIFY = 14400; // 4 hours

    public function run($args)
    {
      $users = User::model()->findAll();

      foreach ($users as $user) {
        Controller::setUserSpecificData($user);
        $lastNotifyTime = max(time() - self::EVENT_NOTIFY, $user->lastEventNotifyTime);

        $events = $this->getEvents($user->id, $lastNotifyTime);
        if (!empty($events)) {
          $template = Yii::app()->mailer->getTemplate('eventNotify');
          if ($template !== null) {
            $data = array();
            foreach ($events as $event) {
              $data[] = strtr($template['event'], array(
                '{name}'     => $event['name'],
                '{appeared}' => Yii::app()->format->formatDatetime($event['appeared']),
                '{type}'     => $event['type'],
                '{event}'    => $event['event'],
              ));
            }

            $params = array(
              '{events}'     => implode(PHP_EOL, $data),
              '{siteName}'   => Yii::app()->name,
              '{siteUrl}'    => Yii::app()->params['siteUrl'],
              '{adminEmail}' => Yii::app()->params['adminEmail'],
              '{loginUrl}'   => Yii::app()->params['siteUrlLogin'],
            );

            Yii::app()->mailer->send(
              $user->email,
              $template['subject'],
              $template['body'],
              $params,
              $template['isHtml'],
              $template['attachments'],
              $template['embeddings']
            );
          }
        }
        $user->lastEventNotifyTime = time();

        if ($this->isExpireNotifyTimeReached($user)) {
          $expires = $this->getExpires($user->id);
          if (!empty($expires)) {
            $template = Yii::app()->mailer->getTemplate('expireNotify');
            if ($template !== null) {
              foreach ($expires as $expire) {
                $data = array();
                $left = floor(($expire['date']-time())/86400);
                $data[] = strtr($template['expire'],array(
                  '{name}'   => $expire['name'],
                  '{status}' => $expire['status'],
                  '{date}'   => Yii::app()->format->formatDate($expire['date']) . ($left > 0 ? ' (' . Yii::t('common','{n} day left|{n} days left',array($left)) . ')' : ''),
                ));
              }

              $params = array(
                '{expires}'    => implode(PHP_EOL,$data),
                '{siteName}'   => Yii::app()->name,
                '{siteUrl}'    => Yii::app()->params['siteUrl'],
                '{adminEmail}' => Yii::app()->params['adminEmail'],
                '{loginUrl}'   => Yii::app()->params['siteUrlLogin'],
              );

              Yii::app()->mailer->send(
                $user->email,
                $template['subject'],
                $template['body'],
                $params,
                $template['isHtml'],
                $template['attachments'],
                $template['embeddings']
              );
            }
          }
          $user->lastExpireNotifyTime = time();
        }

        if ($this->isAlertNotifyTimeReached($user)) {
          $alerts = $this->getAlerts($user->id, $user->alertNotify == User::NOTIFY_ONE_TIME);
          if (!empty($alerts)) {
            $template = Yii::app()->mailer->getTemplate('alertNotify');
            if ($template !== null) {
              foreach ($alerts as $alert) {
                $data = array();
                $data[] = strtr($template['alert'], array(
                  '{name}'     => $alert['name'],
                  '{status}'   => $alert['status'],
                  '{appeared}' => Yii::app()->format->formatDatetime($alert['appeared']),
                  '{alert}'    => $alert['alert'],
                ));
              }

              $params = array(
                '{alerts}'     => implode(PHP_EOL, $data),
                '{siteName}'   => Yii::app()->name,
                '{siteUrl}'    => Yii::app()->params['siteUrl'],
                '{adminEmail}' => Yii::app()->params['adminEmail'],
                '{loginUrl}'   => Yii::app()->params['siteUrlLogin'],
              );

              Yii::app()->mailer->send(
                $user->email,
                $template['subject'],
                $template['body'],
                $params,
                $template['isHtml'],
                $template['attachments'],
                $template['embeddings']
              );
            }
          }
          $user->lastAlertNotifyTime = time();
        }

        $user->save(false);
      }
    }

  public function getEvents($userID,$lastNotifyTime)
  {
    $return = array();
    $criteria = new CDbCriteria;
    $criteria->compare('t.idUser', $userID);
    $criteria->addBetweenCondition('t.create', $lastNotifyTime, time());
    $criteria->with = array('domain');
    $events = DomainEvent::model()->findAll($criteria);
    foreach ($events as $event) {
      $return[] = array(
        'name'     => mb_convert_case($event->name, MB_CASE_UPPER, Yii::app()->charset),
        'appeared' => $event->create,
        'type'     => $event->getAttributeTypeLabel(),
        'event'    => Yii::t('events', $event->event, $event->getParam()),
      );
    }
    return $return;
  }

  public function getExpires($userID)
  {
    $return = array();
    $criteria = new CDbCriteria;
    $criteria->condition = "t.expire IS NOT NULL";
    $criteria->compare('t.expire', '<' . date('Y-m-d', time() + User::EXPIRING_DOMAINS_FORECAST));
    $criteria->compare('t.idUser', $userID);
    $expires = Domain::model()->findAll($criteria);
    foreach ($expires as $expire) {
      $return[] = array(
        'name'   => mb_convert_case($expire->getDomainName(false), MB_CASE_UPPER, Yii::app()->charset),
        'status' => $expire->getAttributeStatusLabel(),
        'date'   => strtotime($expire->expire),
      );
    }
    return $return;
  }

  public function getAlerts($userID, $oneTime = false)
  {
    $return = array();
    $criteria = new CDbCriteria;
    $criteria->compare('t.idUser', $userID);
    if ($oneTime) {
      $criteria->compare('t.notified', '0');
    }
    $alerts = Alert::model()->with('domain')->findAll($criteria);
    foreach ($alerts as $alert) {
      $return[] = array(
        'name'     => mb_convert_case($alert->domain->getDomainName(false), MB_CASE_UPPER, Yii::app()->charset),
        'status'   => $alert->domain->getAttributeStatusLabel(),
        'appeared' => strtotime($alert->create),
        'alert'    => Yii::t('alerts', $alert->alert),
      );
      if (!$alert->notified) {
        $alert->notified = 1;
        $alert->save(false);
      }
    }
    return $return;
  }

  public function isExpireNotifyTimeReached($user)
  {
    switch ($user->expireNotify) {
      case User::NOTIFY_DISABLED:
        return false;
      case User::NOTIFY_EVERY_HOUR:
        $timeout = 3600;
        break;
      case User::NOTIFY_EVERY_FOUR_HOURS:
        $timeout = 14400;
        break;
      case User::NOTIFY_EVERY_TWELVE_HOURS:
        $timeout = 43200;
        break;
      case User::NOTIFY_DAILY:
        $timeout = 86400;
        break;
      case User::NOTIFY_EVERY_THREE_DAYS:
        $timeout = 259200;
        break;
      case User::NOTIFY_EVERY_FIVE_DAYS:
        $timeout = 432000;
        break;
      case User::NOTIFY_WEEKLY:
        $timeout = 604800;
        break;
      default:
        $timeout = self::EXPIRE_NOTIFY;
    }
    return time() >= $user->lastExpireNotifyTime + $timeout;
  }

  public function isAlertNotifyTimeReached($user)
  {
    switch ($user->alertNotify) {
      case User::NOTIFY_DISABLED:
        return false;
      case User::NOTIFY_ONE_TIME:
        return true;
      case User::NOTIFY_EVERY_HOUR:
        $timeout = 3600;
        break;
      case User::NOTIFY_EVERY_FOUR_HOURS:
        $timeout = 14400;
        break;
      case User::NOTIFY_EVERY_TWELVE_HOURS:
        $timeout = 43200;
        break;
      case User::NOTIFY_DAILY:
        $timeout = 86400;
        break;
      case User::NOTIFY_WEEKLY:
        $timeout = 604800;
        break;
      default:
        $timeout = self::EXPIRE_NOTIFY;
    }
    return time() >= $user->lastExpireNotifyTime + $timeout;
  }
}
