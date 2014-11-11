<?php
namespace Ytake\Container;

use Closure;
use ReflectionClass;
use Ytake\Container\Container;
use Illuminate\Container\BindingResolutionException;

/**
 * Class NewInstance
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class NewInstance
{

    /** @var Container  */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $concrete
     * @param array $parameters
     * @return mixed
     */
    public function build($concrete, $parameters = [])
    {
        $instances = [];
        $reflector = new ReflectionClass($concrete);

        $constructor = $reflector->getConstructor();
        /** コンストラクタ無し */
        if (is_null($constructor)) {
            return new $concrete;
        }

        if (!$reflector->isInstantiable()) {
            $message = "Target [$concrete] is not instantiable.";
            throw new BindingResolutionException($message);
        }

        $dependencies = $constructor->getParameters();
        $parameters = $this->container->keyParametersByArgument($dependencies, $parameters);
        $instances = $this->container->getDependencies($dependencies, $parameters);
        return $reflector->newInstanceArgs($instances);

        //var_dump($concrete, $this->container, $reflector);
        /*
        // $autoWired = $this->getAutowired($reflector);
$autoWired = null;
        if (!$reflector->isInstantiable()) {
            $message = "Target [$concrete] is not instantiable.";
            throw new BindingResolutionException($message);
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {

            if($autoWired) {
                $reflectionClass = $this->invokeCompiledClass($autoWired);
                foreach($autoWired as $depend) {
                    if(is_object($depend)) {
                        $instances[] = $depend;
                    }
                }
                return $reflectionClass->newInstanceArgs($instances);
            }
            return new $concrete;
        }

        $reflectionClass = $this->invokeCompiledClass($reflector);
        $dependencies = $constructor->getParameters();

        $parameters = $this->container->keyParametersByArgument($dependencies, $parameters);
        $instances = $this->container->getDependencies($dependencies, $parameters);
        */
    }

} 