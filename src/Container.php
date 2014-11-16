<?php
namespace Ytake\Container;

use Closure;
use ReflectionClass;
use Ytake\Container\Annotation\Finder;
use Ytake\Container\Annotation\Resolver;
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

    /** @var array  */
    protected $dependencies = [];

    /** @var \Doctrine\Common\Annotations\Reader  */
    protected $reader;

    /** @var Finder  */
    protected $finder;

    /** @var Resolver  */
    protected $resolver;

    /** @var array  */
    protected $relations = [];

    const ANNOTATION_SUFFIX = '\\Ytake\\Container\\Annotation\\Annotations\\';

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
     */
    public function setContainer()
    {
        require_once $this->compiler->getCompiledFile();
        $this->reader = $this->compiler->getAnnotationReader();
        $this->resolver = new Resolver;
        $this->finder = new Finder($this->resolver);
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
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        // to parent
        if(is_null($this->compiler)) {
            return parent::build($concrete, $parameters);
        }
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            $message = "Target [$concrete] is not instantiable.";
            throw new BindingResolutionException($message);
        }
        $constructor = $reflector->getConstructor();

        $this->annotated = false;
        $this->propertyResolver($reflector);

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
     * @param array $parameters
     * @return object
     */
    protected function newInstance(array $parameters)
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
        $cache = md5($name);
        $file = $this->compiler->getCompilationDirectory() . '/' . md5($cache) . '$internal.cache.php';
        if (count($reflector->getProperties())) {
            if ($this->finder->exists($file)) {
                $this->dependencies = require $file;
                $this->annotated = true;
            } else {
                /** @var \ReflectionProperty $property */
                foreach ($reflector->getProperties() as $property) {
                    $autoWired = $this->reader->getPropertyAnnotation(
                        $property, "Ytake\Container\Annotation\Annotations\Autowired"
                    );
                    if($autoWired) {
                        $this->dependencies[$name][$property->getName()] = $autoWired->resolver();
                        $this->annotated = true;
                    }
                    $value = $this->reader->getPropertyAnnotation(
                        $property, "Ytake\Container\Annotation\Annotations\Value"
                    );
                    if($value) {
                        $this->dependencies[$name][$property->getName()] = $this->relations[$value->value];
                        $this->annotated = true;
                    }
                }
                if(isset($this->dependencies[$name])) {
                    $this->finder->putRelationFile($file, $this->dependencies);
                    $this->propertyResolver($reflector);
                }
            }
        }
    }

}