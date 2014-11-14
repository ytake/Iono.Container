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


    protected $dependencies = [];

    protected $a = [];

    /** @var \Doctrine\Common\Annotations\Reader  */
    protected $reader;

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
        require_once $this->compiler->getCompilePath() . "/scanned.binding.php";
        $this->reader = $this->compiler->getAnnotationReader();
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
        /** @todo bottleNeck */
        $this->propertyResolver($reflector, $reflector->getName());

        if (is_null($constructor)) {
            if($this->annotated) {
                return $this->newInstance($parameters);
            }
            return new $concrete;
        }

        if($this->annotated) {
            return $this->newInstance($parameters);
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies, $parameters);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * @param array $array
     * @return ReflectionClass
     */
    protected function compilation(array $array)
    {
        return new ReflectionClass($this->compiler->builder($array));
    }

    /**
     * @param array $parameters
     * @return object
     */
    protected function newInstance(array $parameters)
    {
        $reflectionClass = $this->compilation($this->dependencies);
        $constructor = $reflectionClass->getConstructor();
        $dependencies = $constructor->getParameters();
        $parameters = $this->keyParametersByArgument($dependencies, $parameters);
        $instances = $this->getDependencies($dependencies, $parameters);
        return $reflectionClass->newInstanceArgs($instances);
    }

    /**
     * @todo
     * @param ReflectionClass $reflector
     * @return void
     */
    protected function propertyResolver(ReflectionClass $reflector, $name)
    {
        if(count($reflector->getProperties())) {
            foreach ($reflector->getProperties() as $property) {
                /** @var \Ytake\Container\Annotation\Annotations\Autowired $autoWired */
                $autoWired = $this->reader->getPropertyAnnotation($property, "\Ytake\Container\Annotation\Annotations\Autowired");
                if($autoWired) {
                    $this->dependencies[$name][$property->getName()] = $autoWired->resolver();
                    $this->annotated = true;
                }
            }
        }
    }

    /**
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}