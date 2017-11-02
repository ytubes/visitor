<?php
namespace Module\Visitor\Traits;

use Yii;
use yii\di\Container;

/**
 * @property-read Container $di
 */
trait ContainerAwareTrait
{
    /**
     * @return Container
     */
    public function getDi()
    {
        return Yii::$container;
    }
    /**
     * Gets a class from the container.
     *
     * @param string $class  he class name or an alias name (e.g. `foo`) that was previously registered via [[set()]]
     *                       or [[setSingleton()]]
     * @param array  $params constructor parameters
     * @param array  $config attributes
     *
     * @return object
     */
    public function make($class, $params = [], $config = [])
    {
        return $this->getDi()->get($class, $params, $config);
    }
}