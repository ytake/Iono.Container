<?php
namespace Ytake\Container;

use Closure;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Container\BindingResolutionException;

/**
 * Class Container
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Container extends \Illuminate\Container\Container
{

    /** @var Compiler  */
    protected $compiler;

    /** @var bool  */
    protected $readable = false;

    /** @var bool  */
    protected $annotated = false;

    /**
     * @param Compiler $compiler
     */
    public function __construct(Compiler $compiler = null)
    {
        $this->compiler = $compiler;
    }

    /**
     * get component annotation scanned files
     * @return $this
     */
    public function getBean()
    {
        $file = file_get_contents($this->compiler->getCompilePath() . "/scanned.binding.php");
        if($file) {
            $unSerialized = unserialize($file);
            if(count($unSerialized)) {
                foreach ($unSerialized as $key => $bind) {
                    $this->bind($key, $bind['binding']);
                }
                $this->readable = true;
            }
        }
        return $this;
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
        // to parent
        if(is_null($this->compiler)) {
            return parent::build($concrete, $parameters);
        }
        $instances = [];
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            $message = "Target [$concrete] is not instantiable.";
            throw new BindingResolutionException($message);
        }
        $constructor = $reflector->getConstructor();

        $this->annotated = false;
        $resolveInstance = $this->propertyResolver($reflector);

        if (is_null($constructor)) {
            if($this->annotated) {
                $reflectionClass = $this->compilation($resolveInstance);
                foreach($resolveInstance as $depend) {
                    if(is_object($depend)) {
                        $instances[] = $depend;
                    }
                }
                return $reflectionClass->newInstanceArgs($instances);
            }
            return new $concrete;
        }

        if($this->annotated) {
            $reflectionClass = $this->compilation($resolveInstance);
            $constructor = $reflectionClass->getConstructor();
        }

        $dependencies = $constructor->getParameters();
        $parameters = $this->keyParametersByArgument($dependencies, $parameters);

        if($this->annotated) {
            $instances = $this->getDependencies($dependencies, $parameters);
            return $reflectionClass->newInstanceArgs($instances);
        }
        $instances = $this->getDependencies($dependencies, $parameters);
        return $reflector->newInstanceArgs($instances);
    }

    /**
     * @param ReflectionClass $reflector
     * @return ReflectionClass
     */
    protected function compilation(ReflectionClass $reflector)
    {
        $compiler = $this->compiler->builder($reflector);
        return new ReflectionClass($compiler);
    }

    /**
     * @param ReflectionClass $reflector
     * @return null|ReflectionClass
     */
    protected function propertyResolver(ReflectionClass $reflector)
    {

        if(count($reflector->getProperties())) {
            /** @var \ReflectionProperty $property */
            foreach ($reflector->getProperties() as $property) {
                $propertyAnnotations = $this->compiler->getAnnotationReader()->getPropertyAnnotations($property);
                if($propertyAnnotations) {
                    foreach ($propertyAnnotations as $annotation) {
                        if ($annotation instanceof \Ytake\Container\Annotation\Annotations\Autowired) {

                            $property->setAccessible(true);
                            $property->setValue($reflector, $this->make($annotation->value));
                            $this->annotated = true;
                        }
                    }
                }
            }
        }
        return $reflector;
    }

    /**
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}