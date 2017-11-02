<?php
namespace RS\Visitor\Widget;

use Yii;
use yii\base\Widget;

use RS\Visitor\Report\SummaryReport;
/**
 * Class Menu
 * @package RS\Visitor\Widget\DailyStats
 */
class DailyStats extends Widget
{
    public $template = '@RS/Visitor/Resources/views/widgets/dailyStats';

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
