<?php
namespace Ytake\Container;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use Illuminate\Container\BindingResolutionException;

/**
 * Class Container
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Container extends \Illuminate\Container\Container
{

    const CACHING_KEY = "container.bindings";

    /** @var   */
    protected static $instance;

    /** @var  string $base base path */
    protected $base = null;

    /** @var null|Compiler */
    protected $compiler;

    /** @var Container  */
    protected $container;

    /**
     * @param Compiler $compiler
     */
    public function __construct(Compiler $compiler = null)
    {
        $this->getBasePath();
        $this->compiler = (!is_null($compiler)) ? $compiler : null;
    }

    /**
     * @param  string  $concrete
     * @param  array   $parameters
     * @return mixed
     * @throws BindingResolutionException
     * @see \Illuminate\Container\Container::build
     */
    public function build($concrete, $parameters = [])
    {
        if(is_null($this->compiler)) {
            return parent::build($concrete, $parameters);
        }

        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        //var_dump($concrete, $parameters);
        $reflection = $this->compiler->newInstance($this)->build($concrete, $parameters);
        return $this->compiler->compiler($reflection);
    }
/*
    /**
     * @param ReflectionClass $reflector
     * @return ReflectionClass
     *
    protected function invokeCompiledClass(ReflectionClass $reflector)
    {
        $compiler = new Compiler(new Manager(), $reflector, $this);
        return new ReflectionClass($compiler->builder());
    }
*/
    /**
     * @param ReflectionClass $refactor
     * @return null|ReflectionClass
     */
    protected function getAutowired(ReflectionClass $refactor)
    {
        $this->getBinder();
        if(count($refactor->getProperties())) {
            foreach ($refactor->getProperties() as $property) {
                $propertyAnnotations = $this->manager->reader()->getPropertyAnnotations($property);
                foreach($propertyAnnotations as $annotation) {
                    if($annotation instanceof \Ytake\Container\Annotations\Annotation\Autowired) {
                        $this->getService($annotation->value);
                        $property->setAccessible(true);
                        $property->setValue($refactor, $this->make($annotation->value));
                    }
                }
            }
            return $refactor;
        }
        return null;
    }

    /**
     * @param $className
     */
    protected function getService($className)
    {
        $cacheValue = apc_fetch(self::CACHING_KEY);
        $binder = unserialize($cacheValue)[$className];
        $this->bind($className, $binder['binding']);
    }

    /**
     * @return void
     */
    public function getBinder()
    {
        if(!file_exists($this->getBasePath() . "/scanned.binding.php")) {
            file_put_contents($this->getBasePath() . "/scanned.binding.php", null);
        }
        $file = file_get_contents($this->getBasePath() . "/scanned.binding.php");
        apc_store(self::CACHING_KEY, $file);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        $this['container.base.path'] =
            (is_null($this->base)) ? dirname(realpath(__DIR__)) . "/resource" : $this->base;
        return $this->base;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setBasePath($path)
    {
        $this->base = $path;
        $this->instance('container.base.path', $this->base);
        return $this;
    }

    public function keyParametersByArgument(array $dependencies, array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (is_numeric($key)) {
                unset($parameters[$key]);
                $parameters[$dependencies[$key]->name] = $value;
            }
        }
        return $parameters;
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array  $parameters
     * @param  array  $primitives
     * @return array
     */
    public function getDependencies($parameters, array $primitives = array())
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (array_key_exists($parameter->name, $primitives)) {
                $dependencies[] = $primitives[$parameter->name];
            } elseif (is_null($dependency)) {
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }
        return (array) $dependencies;
    }

}