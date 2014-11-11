<?php
namespace Ytake\Container;

use Closure;
use ReflectionClass;
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
        $this->container = $this;
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

        var_dump($this->compiler->newInstance(new self));
exit;
        $instances = [];
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        $autoWired = $this->getAutowired($reflector);

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

        $parameters = $this->keyParametersByArgument($dependencies, $parameters);
        $instances = $this->getDependencies($dependencies, $parameters);

        return $reflectionClass->newInstanceArgs($instances);
    }

    /**
     * @param ReflectionClass $reflector
     * @return ReflectionClass
     */
    protected function invokeCompiledClass(ReflectionClass $reflector)
    {
        $compiler = new Compiler(new Manager(), $reflector, $this);
        return new ReflectionClass($compiler->builder());
    }

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
}