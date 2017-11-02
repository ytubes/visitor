<?php
namespace Module\Visitor\Event;

use Yii;

use Module\Visitor\Tracker\Visit;
use Module\Visitor\Model\Visitor;

class VisitEvent
{
    public static function onVisit($event)
    {
        $visit = new Visit();
        $tableName = Visitor::tableName();

        if ($visit->isCrawler()) {
        	return;
        }

        $session = Yii::$app->session;
        $session->open();

        $visitTime = (new \DateTime())
            ->format('Y-m-d H:i:s');

        $limitAgo = (new \DateTime()) // sinceLastVisit
            ->sub(new \DateInterval('PT7200S'));

        $lastVisit = $session->get('last_visit', false);

        $firstVisit = false;
        if (false === $lastVisit) {
            $firstVisit = true;
        } else {
            $lastVisit = (new \DateTime($lastVisit));

            // Если с момента последнего визита прошло более чем полчаса, то будем считать новый уник.
            if ($lastVisit < $limitAgo) {
                $firstVisit = true;
            // Если с момента последнего клика прошло меньше 10 секунд, значит это бот
            } else {
                $lastClick = $session->get('last_click', false);
                $lastClick = (new \DateTime($lastClick));
                $fastClick = (new \DateTime())
                    ->sub(new \DateInterval('PT3S'));

                if ($lastClick !== false && $lastClick > $fastClick) {
                    return;
                }
            }
        }

        $ip = inet_pton($visit->getIp());
        $ref_group = $visit->getRefererType();

        if (true === $firstVisit) {
            $device_group = $visit->getDeviceType();
            $ref_site = $visit->getRefererHost();

            if (!$ref_site) { // заплатка
                $ref_site = '';
            }

            $sql = "
                INSERT INTO `{$tableName}` (`ip`, `first_visit`, `last_visit`, `session_time`, `raw_in`, `views`, `clicks`, `ref_site`, `ref_group`, `device_group`)
                VALUES (:ip, :first_visit, NULL, 0, 1, 1, 0, :ref_site, :ref_group, :device_group)";
            Yii::$app->db->createCommand($sql)
                ->bindValue(':ip', $ip)
                ->bindValue(':first_visit', $visitTime)
                ->bindValue(':ref_site', $ref_site)
                ->bindValue(':ref_group', $ref_group)
                ->bindValue(':device_group', $device_group)
                ->execute();

            $session->set('last_visit', $visitTime);
        } else {
                // Если переход на сайт извне, то считаем повторный вход
            $raw_in = 0;
            $click = 0;

            if ($ref_group !== 'internal') {
                $raw_in = 1;
            // Если это внутренний переход, то считаем клик дополнительно.
            } else {
                $click = 1;
            }
            // Время обновления берем из сессии + текуший ип
            $timestamp = $lastVisit->format('Y-m-d H:i:s');

            $sql = "
                UPDATE `{$tableName}`
                SET `last_visit`=:last_visit, `session_time`=TIMESTAMPDIFF(SECOND,`first_visit`,`last_visit`), `raw_in`=`raw_in`+:raw_in, `views`=`views`+1, `clicks`=`clicks`+:click
                WHERE `ip`=:ip AND `first_visit`=TIMESTAMP(:timestamp)";
            Yii::$app->db->createCommand($sql)
                ->bindValue(':last_visit', $visitTime)
                ->bindValue(':raw_in', $raw_in)
                ->bindValue(':click', $click)
                ->bindValue(':ip', $ip)
                ->bindValue(':timestamp', $timestamp)
                ->execute();
        }

        $session->set('last_click', $visitTime);
    }
}
