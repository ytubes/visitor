<?php
namespace Module\Visitor\Event;

use Yii;
use yii\db\Query;
use Module\Visitor\Model\Visitor;

class ReportEvent
{
	/**
	 * Удаляет ипы из таблицы, у которых было слишком много заходов
	 *
	 * @return void
	 */
    public static function deleteVisitsOverhead($event)
    {
		$ips = (new Query())
		    ->select(['ip', 'COUNT(*) as `visit_num`'])
		    ->from(Visitor::tableName())
		    ->groupBy(['ip'])
		    ->having(['>', 'visit_num', 5])
		    ->all();

        if (!empty($ips)) {
	        $db = Yii::$app->get('db');

	        foreach ($ips as $ip) {
	        	$db->createCommand()
	        		->delete(Visitor::tableName(), '`ip`=:ip')
	        		->bindValue(':ip', $ip['ip'])
	        		->execute();
	        }
	    }
    }
}
