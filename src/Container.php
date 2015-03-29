<?php
namespace Iono\Container;

use Closure;
use ReflectionClass;
use Illuminate\Container\BindingResolutionException;

/**
 *
 * {@inheritdoc}
 * Class Container
 * @package Iono\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Container extends \Illuminate\Container\Container
{

    /** @var CompilerInterface */
    protected $compiler;

    /** @var bool */
    protected $readable = false;

    /** @var bool */
    protected $annotated = false;

    /** @var array */
    protected $dependencies = [];

    /** @var \Doctrine\Common\Annotations\Reader */
    protected $reader;

    /** @var array */
    protected $map = [];

    /**
     * @param CompilerInterface $compiler
     */
    public function __construct(CompilerInterface $compiler = null)
    {
        $this->compiler = $compiler;
    }

    /**
     * get component annotation scanned files
     * @return $this
     * @throws \Exception
     */
    public function register()
    {
        if (!file_exists($this->compiler->getCompiledFile())) {
            throw new \Exception("annotation scanned file ot found");
        }
        require $this->compiler->getCompiledFile();
        $this->reader = $this->compiler->getAnnotationManager();
        return $this;
    }

    /**
     * @param  string $concrete
     * @param  array $parameters
     * @return mixed
     * @throws BindingResolutionException
     * @throws \ErrorException
     * @see \Illuminate\Container\Container::build
     */
    public function build($concrete, $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        // to parent
        if (is_null($this->compiler)) {
            return parent::build($concrete, $parameters);
        }
        if (is_null($this->reader)) {
            throw new \ErrorException("method Container::register() must be called");
        }
        $reflector = $this->instantiable($concrete);
        $constructor = $reflector->getConstructor();

        $this->annotated = false;
        $this->propertyResolver($reflector);

        if (is_null($constructor)) {
            if ($this->annotated) {
                return $this->resolveInstance($parameters);
            }
            return new $concrete;
        }
        if ($this->annotated) {
            return $this->resolveInstance($parameters);
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies, $parameters);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve the given type from the container.
     * Rename method
     * @param  string $abstract
     * @param  array $parameters
     * @return mixed
     */
    public function newInstance($abstract, $parameters = [])
    {
        $abstract = $this->getAlias($abstract);
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        $concrete = $this->getConcrete($abstract);

        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->newInstance($concrete, $parameters);
        }

        foreach ($this->getExtenders($abstract) as $extender) {
            $object = $extender($object, $this);
        }
        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }
        $this->fireResolvingCallbacks($abstract, $object);
        $this->resolved[$abstract] = true;
        return $object;
    }

    /**
     * @param array $parameters
     * @return object
     */
    protected function resolveInstance(array $parameters)
    {
        $reflectionClass = $this->compiler->getCompilation($this->dependencies);
        $constructor = $reflectionClass->getConstructor();
        $dependencies = $constructor->getParameters();
        $parameters = $this->keyParametersByArgument($dependencies, $parameters);
        $instances = $this->getDependencies($dependencies, $parameters);
        return $reflectionClass->newInstanceArgs($instances);
    }

    /**
     * @param ReflectionClass $reflector
     * @return void
     */
    protected function propertyResolver(ReflectionClass $reflector)
    {

        $name = $reflector->getName();
        $file = $this->compiler->getPropertyCompiledFile($name);

        if (count($reflector->getProperties())) {

            if (file_exists($file) && !$this->compiler->getForce()) {
                $this->dependencies = require $file;
                $this->annotated = true;

            } else {
                /** @var \ReflectionProperty $property */
                foreach ($reflector->getProperties() as $property) {
                    $autoWired = $this->reader->getPropertyAnnotation(
                        $property, "Iono\Container\Annotation\Annotations\Autowired"
                    );

                    if ($autoWired) {
                        $this->dependencies[$name][$property->getName()] = $autoWired->resolver();
                    }
                    $value = $this->reader->getPropertyAnnotation(
                        $property, "Iono\Container\Annotation\Annotations\Value"
                    );
                    if ($value) {
                        $this->dependencies[$name][$property->getName()] = $value->value;
                    }
                }
                if (isset($this->dependencies[$name])) {
                    $this->annotated = true;
                    $this->compiler->putPropertyCompiledFile($file, $this->dependencies);

                    // $this->propertyResolver($reflector);
                }
            }
        }
    }

    /**
     * @param $concrete
     * @return ReflectionClass
     * @throws BindingResolutionException
     */
    protected function instantiable($concrete)
    {
        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            $message = "Target [$concrete] is not instantiable.";
            throw new BindingResolutionException($message);
        }
        return $reflector;
    }

    /**
     * {@inheritdoc}
     * @see \Illuminate\Container\Container::resolveNonClass
     */
    protected function resolveNonClass(\ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        if (isset($this->map[$parameter->getName()])) {
            return $this->make($parameter->getName());
        }
        $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";
        throw new BindingResolutionException($message);
    }
}
