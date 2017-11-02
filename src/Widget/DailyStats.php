<?php
namespace Module\Visitor\Widget;

use Yii;
use yii\base\Widget;

use Module\Visitor\Report\SummaryReport;
/**
 * Class Menu
 * @package Module\Visitor\Widget\DailyStats
 */
class DailyStats extends Widget
{
    public $template = '@Module/Visitor/Resources/views/widgets/dailyStats';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

	public function run()
	{
		$reportBuilder = new SummaryReport;
		$data = $reportBuilder->build();

        return $this->render($this->template, [
        	'data' => $data,
        ]);
	}
}
