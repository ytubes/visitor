<?php
namespace RS\Visitor\Controller;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use RS\Visitor\Model\Visitor;
use RS\Visitor\Report\SummaryReport;
use RS\Visitor\Traits\ContainerAwareTrait;

class StatsController extends Controller
{
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
	       'access' => [
	           'class' => AccessControl::class,
               'rules' => [
                   [
                       'allow' => true,
                       'roles' => ['@'],
                   ],
               ],
	       ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $reportBuilder = new SummaryReport;

        $reportBuilder->on(SummaryReport::EVENT_BEFORE_BUILD, [\RS\Visitor\Event\ReportEvent::class, 'deleteVisitsOverhead']);

        $report = $reportBuilder->build();

        return $this->render('index', [
        	'data' => $report,
        ]);
    }

    public function actionDetail()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Visitor::find(),
            'pagination' => [
		        'pageSize' => 200,
		    ],
        ]);

        return $this->render('detail', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
