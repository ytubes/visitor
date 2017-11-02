<?php
namespace RS\Visitor;

use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\i18n\PhpMessageSource;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;

use RS\Visitor\Event\VisitEvent;

class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('visitor') && $app->getModule('visitor') instanceof Module) {
            Yii::setAlias('@RS/Visitor', __DIR__);

            $this->initContainer($app);
            $this->initTranslations($app);
            //$this->initMailServiceConfiguration($app, $app->getModule('user'));

            if ($app instanceof WebApplication) {
                $this->initControllerNamespace($app);
                $this->initUrlRoutes($app);
                $this->initEvents($app);
                //$this->initAuthCollection($app);
                //$this->initAuthManager($app);

            } else {
                /* @var $app ConsoleApplication */
                $this->initConsoleCommands($app);
                //$this->initAuthManager($app);
            }
        }
    }
    /**
     * Registers controllers.
     *
     * @param WebApplication $app
     */
    protected function initControllerNamespace(WebApplication $app)
    {
        $app->getModule('visitor')->controllerNamespace = 'RS\Visitor\Controller';
        $app->getModule('visitor')->setViewPath('@RS/Visitor/Resources/views');
    }
    /**
     * Registers console commands to main app.
     *
     * @param ConsoleApplication $app
     */
    protected function initConsoleCommands(ConsoleApplication $app)
    {
        $app->getModule('visitor')->controllerNamespace = 'RS\Visitor\Command';
    }
    /**
     * Registers module translation messages.
     *
     * @param Application $app
     */
    protected function initTranslations(Application $app)
    {
        if (!isset($app->get('i18n')->translations['visitor*'])) {
            $app->get('i18n')->translations['visitor*'] = [
                'class' => PhpMessageSource::class,
                'basePath' => __DIR__ . '/Resources/i18n',
                'sourceLanguage' => 'en-US',
            ];
        }
    }
    /**
     * Registers routes
     *
     * @param Application $app
     */
    protected function initUrlRoutes(Application $app)
    {
        $app->getUrlManager()->addRules([
            [
                'class' => \yii\web\UrlRule::class,
                'pattern' => '/api/visitor/<action:[\w\-]+>',
                'route' => '/visitor/api/<action>',
            ],
        ], false);
    }
    /**
     * Initialize container with module classes.
     *
     * @param \yii\base\Application $app
     * @param array                 $map the previously built class map list
     */
    protected function initContainer($app)
    {
        $di = Yii::$container;
        try {

        } catch (Exception $e) {
            die($e);
        }
    }
    protected function initEvents($app)
    {
    	Event::on(\frontend\controllers\SiteController::class, \frontend\controllers\SiteController::EVENT_AFTER_INDEX_SHOW, [VisitEvent::class, 'onVisit']);

    	if ($app->hasModule('videos')) {
    		Event::on(\ytubes\videos\controllers\CategoryController::class, \ytubes\videos\controllers\CategoryController::EVENT_AFTER_CATEGORY_SHOW, [VisitEvent::class, 'onVisit']);
    		Event::on(\ytubes\videos\controllers\RecentController::class, \ytubes\videos\controllers\RecentController::EVENT_AFTER_RECENT_SHOW, [VisitEvent::class, 'onVisit']);
    		Event::on(\ytubes\videos\controllers\ViewController::class, \ytubes\videos\controllers\ViewController::EVENT_AFTER_VIEW_SHOW, [VisitEvent::class, 'onVisit']);
    	}
    }
}
