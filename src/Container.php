<?php
namespace Ytake\Container;

use Closure;
use ReflectionClass;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use Illuminate\Container\BindingResolutionException;
use Ytake\Container\Annotations\Manager;

/**
 * Class Container
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Container extends \Illuminate\Container\Container
{

    const CACHING_KEY = "container.bindings";

    /** @var Manager  */
    protected $manager;

    public function __construct()
    {
        $this->manager = new Manager();
    }

    /**
     * @param  string  $concrete
     * @param  array   $parameters
     * @return mixed
     * @throws BindingResolutionException
     * @see \Illuminate\Container\Container::build
     */
    public function build($concrete, $parameters = array())
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            $message = "Target [$concrete] is not instantiable.";
            throw new BindingResolutionException($message);
        }

        $constructor = $reflector->getConstructor();
        $selfClass = get_class($this);
        if (is_null($constructor)) {

            $autoWired = $this->getAutowired($reflector);
            if($autoWired) {
                $reflectionClass = $this->invokeCompiledClass($autoWired);
                foreach($autoWired as $depend) {
                    if(is_object($depend)) {
                        $instances[] = $depend;
                    }
                }
                return $reflectionClass->newInstanceArgs(array_merge([new $selfClass], $instances));
            }
            return new $concrete;
        }
        $reflectionClass = $this->invokeCompiledClass($reflector);
        $dependencies = $constructor->getParameters();

        $parameters = $this->keyParametersByArgument($dependencies, $parameters);
        $instances = $this->getDependencies($dependencies, $parameters);

        return $reflectionClass->newInstanceArgs(array_merge([new $selfClass], $instances));
    }

    /**
     * @param ReflectionClass $reflector
     * @return ReflectionClass
     */
    protected function invokeCompiledClass(ReflectionClass $reflector)
    {
        $compiler = new Compiler(
            new BuilderFactory(),
            new Standard(),
            new Manager(),
            $reflector,
            $this
        );
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
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return void
     */
    public function getBinder()
    {
        $file = file_get_contents(dirname(realpath(null)) . "/resource/scanned.binding.php");
        apc_store(self::CACHING_KEY, $file);
    }
}