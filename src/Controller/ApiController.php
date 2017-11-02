<?php
namespace Module\Visitor\Controller;

use Yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;

use Module\Visitor\Traits\ContainerAwareTrait;
use Module\Visitor\Report\SummaryReport;

/**
 * SitesController implements the CRUD actions for Site model.
 */
class ApiController extends Controller
{
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
	       /*'access' => [
	           'class' => AccessControl::class,
               'rules' => [
                   [
                       'allow' => true,
                       'roles' => ['manageSites'],
                   ],
               ],
	       ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],*/
	        'contentNegotiator' => [
	            'class' => ContentNegotiator::class,
	            'only' => ['index'],
	            'formatParam' => '_format',
	            'formats' => [
	                'application/json' => Response::FORMAT_JSON,
	            ],
	        ],
        ];
    }

    /**
     * Lists all Site models.
     * @return mixed
     */
    public function actionIndex()
    {
        $reportBuilder = new SummaryReport;

        $reportBuilder->on(SummaryReport::EVENT_BEFORE_BUILD, [\Module\Visitor\Event\ReportEvent::class, 'deleteVisitsOverhead']);

        $report = $reportBuilder->build();

        return $report;
    }
}
