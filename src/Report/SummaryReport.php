<?php
namespace Module\Visitor\Report;

use Yii;
use yii\base\Component;
use Module\Visitor\Model\Visitor;

/**
 * Visitor stats builder
 */
class SummaryReport extends Component
{
	const EVENT_BEFORE_BUILD = 'beforeBuild';
	const EVENT_AFTER_BUILD = 'afterBuild';

	/**
	 * @var string таблица с визиторами \Module\Visitor\Model\Visitor
	 */
	private $tableName;
	/**
	 * @var array Сводный отчет по статистике.
	 */
	private $data = [];

	public function __construct()
	{
		$this->tableName = Visitor::tableName();
	}
	/**
	 * Формирует сводный отчет по статистике за прошедшие сутки.
	 *
	 * @return array
	 */
	public function build()
	{
		$this->trigger(self::EVENT_BEFORE_BUILD);

		$this->data['last_hour'] = $this->getLast1HStats();
		$this->data['last_day'] = $this->getLast24HStats();
		$this->data['ref_groups'] = $this->getRefGroups();
		$this->data['device_groups'] = $this->getDeviceGroups();

		$this->trigger(self::EVENT_AFTER_BUILD);

		return $this->data;
	}
	/**
	 * Задает интервал за последний час и возвращает статистику за это время
	 *
	 * @param string $timeAgo время.
	 *
	 * @return array
	 */
	public function getLast1HStats()
	{
		$hourAgo = (new \DateTime('-1 hour'))
			->format('Y-m-d H:i:s');

		return $this->getStatsByTimeAgo($hourAgo);
	}
	/**
	 * Задает интервал за последние 24 часа и возвращает статистику за это время
	 *
	 * @param string $timeAgo время.
	 *
	 * @return array
	 */
	public function getLast24HStats()
	{
		$dayAgo = (new \DateTime('-1 day'))
			->format('Y-m-d H:i:s');

		return $this->getStatsByTimeAgo($dayAgo);
	}
	/**
	 * Возвращает массив со статистикой за период времени от текущего момента.
	 *
	 * @param string $timeAgo Время, за которое нужна статистика (не более чем 24 часа)
	 *
	 * @return array
	 */
	private function getStatsByTimeAgo($timeAgo)
	{
        $data = [];

        $sql = "
        	SELECT
        		COUNT(*) as `total`,
        		COUNT(DISTINCT `ip`) as `unique_in`,
        		SUM(`raw_in`) as `raw_in`,
        		SUM(`views`) as `views`,
        		SUM(`clicks`) as `clicks`,
        		(SUM(`session_time`)/COUNT(*))/60 as `session_time`
        	FROM `{$this->tableName}`
        	WHERE `first_visit`>TIMESTAMP('{$timeAgo}')";

		$rows = Yii::$app->db->createCommand($sql)
			->queryOne();

		$total = isset($rows['total']) ? (int) $rows['total'] : 0;
		$unique_in = isset($rows['unique_in']) ? (int) $rows['unique_in'] : 0;
		$raw_in = isset($rows['raw_in']) ? (int) $rows['raw_in'] : 0;
		$views = isset($rows['views']) ? (int) $rows['views'] : 0;
		$clicks = isset($rows['clicks']) ? (int) $rows['clicks'] : 0;
		$session_time = isset($rows['session_time']) ? round((float) $rows['session_time'], 2) : 0;

		$data = [
			'total' => $total,
			'unique_in' => $unique_in,
			'raw_in' => $raw_in,
			'views' => $views,
			'clicks' => $clicks,
			'session_time' => $session_time,
		];

		if ($unique_in > 0) {
			$data['views_per_user'] = round(($views / $unique_in), 1);
			$data['productivity_total'] = round(($clicks * 100) / $unique_in, 1); // 4024*100/3051 = 131,89
		} else {
			$data['views_per_user'] = 0;
			$data['productivity_total'] = 0;
		}

		if ($raw_in > 0) {
			$data['unique_rate'] = floor(($unique_in / $raw_in)  * 100);

			$sql = "SELECT COUNT(*) as `bounced` FROM `{$this->tableName}` WHERE `first_visit`>TIMESTAMP('{$timeAgo}') AND `views`=1";
			$bounced = (int) Yii::$app->db->createCommand($sql)
				->queryScalar();

			$data['bounce_rate'] = round(($bounced / $raw_in) * 100, 1);
		} else {
			$data['unique_rate'] = 0;
			$data['bounce_rate'] = 0;
		}

		return $data;
	}
	/**
	 * Формирует сводные данные по группам рефереров (название|суммарное количество хитов)
	 * Также добавляет в сводный отчет $this->data информацию о процентаже.
	 *
	 * @param string $timeAgo время
	 *
	 * @return array
	 */
	public function getRefGroups($timeAgo = '')
	{
        $data = [];

        if ($timeAgo === '') {
			$timeAgo = (new \DateTime('-1 day'))
				->format('Y-m-d H:i:s');
        }

        $sql = "
        	SELECT COUNT(*) as `total`
        	FROM `{$this->tableName}`
        	WHERE `first_visit`>TIMESTAMP('{$timeAgo}')";

		$total = Yii::$app->db->createCommand($sql)
			->queryScalar();

        $sql = "
        	SELECT `ref_group`, COUNT(*) AS `cnt`
			FROM `{$this->tableName}`
			WHERE `first_visit`>TIMESTAMP('{$timeAgo}')
			GROUP BY `ref_group`";
		$ref_groups = Yii::$app->db->createCommand($sql)
			->queryAll();

		if (!empty($ref_groups)) {
			foreach ($ref_groups as $ref_group) {
				$type = $ref_group['ref_group'];
				$num = (int) $ref_group['cnt'];
				$data[$type] = $num;
			}

			if (!empty($data[Visitor::REF_GROUP_SE]) && $total > 0) {
				$this->data['se_percent'] = floor($data[Visitor::REF_GROUP_SE] / $total * 100);
			}

			if (!empty($data[Visitor::REF_GROUP_BOOKMARK]) && $total > 0) {
				$this->data['bookmarks_percent'] = floor($data[Visitor::REF_GROUP_BOOKMARK] / $total * 100);
			}

			if (!empty($data[Visitor::REF_GROUP_LINKS]) && $total > 0) {
				$this->data['links_percent'] = ceil($data[Visitor::REF_GROUP_LINKS] / $total * 100);
			}

			if (!empty($data[Visitor::REF_GROUP_INTERNAL]) && $total > 0) {
				$this->data['internal_percent'] = ceil($data[Visitor::REF_GROUP_INTERNAL] / $total * 100);
			}
		}

		return $data;
	}
	/**
	 * Формирует сводные данные по группам устройств (название|суммарное количество хитов)
	 * Также добавляет в сводный отчет $this->data информацию о процентаже.
	 *
	 * @param string $timeAgo время
	 *
	 * @return array
	 */
	public function getDeviceGroups($timeAgo = '')
	{
		$data = [];

        if ($timeAgo === '') {
			$timeAgo = (new \DateTime('-1 day'))
				->format('Y-m-d H:i:s');
        }

        $sql = "
        	SELECT COUNT(*) as `total`
        	FROM `{$this->tableName}`
        	WHERE `first_visit`>TIMESTAMP('{$timeAgo}')";

		$total = Yii::$app->db->createCommand($sql)
			->queryScalar();

		$sql = "
			SELECT `device_group`, COUNT(*) AS `cnt`
			FROM `{$this->tableName}`
			WHERE `first_visit`>TIMESTAMP('{$timeAgo}')
			GROUP BY `device_group`";
		$device_groups = Yii::$app->db->createCommand($sql)
			->queryAll();

		if (!empty($device_groups)) {
			foreach ($device_groups as $device_group) {
				$type = $device_group['device_group'];
				$num = (int) $device_group['cnt'];
				$data[$type] = $num;
			}

			if (!empty($data[Visitor::DEVICE_MOBILE]) && $total > 0) {
				$this->data['mobile_percent'] = floor($data[Visitor::DEVICE_MOBILE] / $total * 100);
			}

			if (!empty($data[Visitor::DEVICE_DESKTOP]) && $total > 0) {
				$this->data['desktop_percent'] = ceil($data[Visitor::DEVICE_DESKTOP] / $total * 100);
			}
		}

		return $data;
	}
}
