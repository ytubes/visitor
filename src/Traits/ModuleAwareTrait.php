<?php
namespace RS\Visitor\Traits;

use Yii;
/**
 * @property-read Module $module
 */
trait ModuleAwareTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('visitor');
    }
}