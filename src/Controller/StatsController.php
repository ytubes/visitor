<?php
namespace Module\Visitor\Controller;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use Module\Visitor\Model\Visitor;
use Module\Visitor\Report\SummaryReport;
use Module\Visitor\Traits\ContainerAwareTrait;

class StatsController extends Controller
{
    use ContainerAwareTrait;

    protected $request;
    protected $response;
    protected $session;

    /**
     * MassActionsController constructor.
     *
     * @param string    $id
     * @param Module    $module
     * @param Request   $request
     * @param Response  $response
     * @param Session   $session
     * @param array     $config
     */
    public function __construct($id, Module $module, Request $request, Response $response, Session $session, array $config = [])
    {
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;

        parent::__construct($id, $module, $config);
    }
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

        $reportBuilder->on(SummaryReport::EVENT_BEFORE_BUILD, [\Module\Visitor\Event\ReportEvent::class, 'deleteVisitsOverhead']);

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
