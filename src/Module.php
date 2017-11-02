<?php
namespace RS\Visitor;

use yii\base\Module as BaseModule;
/**
 * This is the main module class of the yii2-usuario extension.
 */
class Module extends BaseModule
{
    /**
     * @var array the class map. How the container should load specific classes
     * @see Bootstrap::buildClassMap() for more details
     */
    public $classMap = [];
    /**
     * @var string
     */
    public $viewPath = '@RS/Visitor/Resources/views';
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'visitor/index/index';
}