<?php
namespace Module\Visitor\Cron\Job;

use Yii;
use yii\base\Event;
use Module\Visitor\Cron\CronJobInterface;
use Module\Visitor\Model\Visitor;

/**
 * Очищение статы пользователей более чем за сутки
 */
class ClearOldVisitorStatJob implements CronJobInterface
{
    public function handle()
    {
    	$this->run();
    }

    public function run()
    {
            // -24 часа от текущего момента.
        $last_day = (new \DateTime('NOW'))
            ->sub(new \DateInterval('P1D'))
            ->format('Y-m-d H:i:s');

        Yii::$app->db->createCommand()
            ->delete(Visitor::tableName(), 'first_visit<TIMESTAMP(:last_day)')
            ->bindValue(':last_day', $last_day)
            ->execute();
    }
}
