<?php
namespace Ytake\Container;

use ReflectionClass;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\PrettyPrinter\Standard;
use Doctrine\Common\Annotations\Reader;

/**
 * Class Compiler
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Compiler extends AbstractCompiler implements CompilerInterface
{

    /** @var BuilderFactory */
    protected $factory;

    /** @var Standard  */
    protected $printer;

    /** @var ReflectionClass  */
    protected $reflectionClass;

    /** @var string  */
    protected $scannedFileName = "scanned.binding.php";

    /** @var string  */
    protected $scannedFileDirectory = "resource";

    /** @var string  */
    protected $compiledFileDirectory = "compiled";

    /** @var null  */
    protected $path = null;

    /** @var bool  */
    protected $force = false;

    /** @var array  */
    protected $arguments = [];

    /** @var array  */
    protected $traits = [];

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->setCompilePath();
    }

    /**
     * @param array $array
     * @return ReflectionClass
     */
    public function getCompilation(array $array)
    {
        $build = $this->builder($array);
        require_once $build['file'];
        return new ReflectionClass($build['class']);
    }

    /**
     * @param array $array
     * @return array
     */
    public function builder(array $array)
    {
        $parameters = [];
        foreach($array as $class => $properties) {

            $className = $this->getClassName($class);
            $compiledClassFile = $this->getCompilationDirectory() . '/' . $className. '.php';
            if(!$this->force) {
                if(file_exists($compiledClassFile)) {
                    return [
                        'file' => $compiledClassFile,
                        'class' => '\\' . $className
                    ];
                }
            }
            $this->deleteCompiledFile();
            $this->activate();
            /** @var \PhpParser\Builder\Method $construct */
            $construct = $this->factory->method('__construct');

            $reflectionClass = new ReflectionClass($class);
            /** @var \ReflectionMethod $constructor */
            $constructor = $reflectionClass->getConstructor();
            if ($constructor) {
                /** @var array $parameters */
                $parameters = $reflectionClass->getConstructor()->getParameters();
            }
            /**  */
            $this->setParameters($parameters, $construct);
            /** set properties(construct) */
            $this->setProperties($properties, $construct);
            /** parent constructor */
            if($constructor) {
                $this->setParentConstructor($constructor, $construct);
            }
            /** set traits */
            $this->setTraits($reflectionClass);

            $node = $this->factory->class($className)
                ->extend("\\" . $reflectionClass->name)->makeFinal()->addStmt($construct);
            if(count($this->traits)) {
                $node = $node->addStmt(new TraitUse($this->traits));
            }
            $this->putCompiledFile($className, [$node->getNode()]);
            return [
                'file' => $compiledClassFile,
                'class' => '\\' . $className
            ];
        }
        return [];
    }


    /**
     * @param $filename
     * @param $stmts
     * @return bool
     */
    protected function putCompiledFile($filename, $stmts)
    {
        $path = $this->getCompilationDirectory() . '/' . $filename. '.php';
        $output = "<?php\n" . $this->printer->prettyPrint($stmts);
        file_put_contents($path, $output);
    }

    /**
     * @param $class
     * @return mixed
     */
    protected function getClassName($class)
    {
        return str_replace('\\', '', $class);
    }

    /**
     * @return null|string
     */
    public function getCompilationDirectory()
    {
        if($this->compiledFileDirectory) {
            return $this->path . '/' . $this->compiledFileDirectory;
        }
        return $this->path;
    }

    /**
     * @return string
     */
    public function getCompiledFile()
    {
        $this->path = (is_null($this->path)) ? dirname(realpath(__DIR__)) . "/{$this->scannedFileDirectory}" : $this->path;
        return $this->path . '/' . $this->scannedFileName;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setCompilePath($path = null)
    {
        $path = (is_null($path)) ? dirname(realpath(__DIR__)) . "/{$this->scannedFileDirectory}" : $path;
        $this->path = $path;
        return $this;
    }

    /**
     * activate file parser
     */
    protected function activate()
    {
        $this->factory = new BuilderFactory;
        $this->printer = new Standard;
    }

    /**
     * annotation getter
     * @return Reader
     */
    public function getAnnotationReader()
    {
        return $this->reader;
    }

    /**
     * @param bool $force
     * @return $this
     */
    public function setForceCompile($force = true)
    {
        $this->force = $force;
        return $this;
    }

    /**
     * @access private
     * @param array $parameters
     * @param \PhpParser\Builder\Method $construct
     */
    private function setParameters(array $parameters, \PhpParser\Builder\Method &$construct)
    {
        if($parameters) {
            /** @var \ReflectionParameter $c */
            foreach ($parameters as $c) {

                if ($c->getClass()) {
                    $construct->addParam($this->factory->param($c->name)->setTypeHint("\\" . $c->getClass()->name));
                }
                if ($c->isDefaultValueAvailable()) {
                    $construct->addParam($this->factory->param($c->name)->setDefault($c->getDefaultValue()));
                }
                $this->arguments[] = new \PhpParser\Node\Arg(new \PhpParser\Node\Expr\Variable($c->name));
            }
        }
    }

    /**
     * @access private
     * @param array $properties
     * @param \PhpParser\Builder\Method $construct
     */
    private function setProperties(array $properties, \PhpParser\Builder\Method &$construct)
    {
        if(count($properties)) {
            $fieldInjector = [];
            foreach ($properties as $key => $param) {
                try {
                    $reflectionClass = new ReflectionClass($param);
                    $construct->addParam(
                        $this->factory->param($key)->setTypeHint("\\" . $reflectionClass->getName())
                    );
                    $fieldInjector[$key] = $key;
                } catch (\ReflectionException $e) {
                    $construct->addParam($this->factory->param($param));
                    $fieldInjector[$key] = $param;
                }
            }
            /** added constructor injection  */
            if (count($fieldInjector)) {
                foreach ($fieldInjector as $target => $inject) {
                    $construct->addStmt(new \PhpParser\Node\Name("\$this->{$target} = \${$inject};"));
                }
            }
        }
    }

    /**
     * @access private
     * @param \ReflectionMethod $constructor
     * @param \PhpParser\Builder\Method $construct
     */
    private function setParentConstructor(\ReflectionMethod $constructor, \PhpParser\Builder\Method &$construct)
    {
        if ($constructor) {
            $construct->addStmt(
                new \PhpParser\Node\Expr\FuncCall(
                    new \PhpParser\Node\Name('parent::__construct'),
                    $this->arguments
                )
            );
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function setTraits(\ReflectionClass $reflectionClass)
    {
        $traits = $reflectionClass->getTraitNames();
        if(count($traits)) {
            foreach ($traits as $trait) {
                $this->traits[] = new \PhpParser\Node\Name("\\" . $trait);
            }
        }
    }

    /**
     * @param string $name
     * @return string
     */
    public function getPropertyCompiledFile($name = '')
    {
        return $this->getCompilationDirectory() . '/' . md5($name) . '$internal.cache.php';
    }

    /**
     * @param $path
     * @param array $context
     * @return int
     */
    public function putPropertyCompiledFile($path, array $context = [])
    {
        $context = "<?php return unserialize('" . serialize($context) . "');";
        return file_put_contents($path, $context);
    }

    /**
     * @return void
     */
    public function deleteCompiledFile()
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->getCompilationDirectory(),
                \RecursiveDirectoryIterator::CURRENT_AS_SELF
            ),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            if ($item->isFile() || $item->isLink()) {
                unlink($item->getPathname());
            } elseif ($item->isDir() && !$item->isDot()) {
                rmdir($item->getPathname());
            }
        }
    }
}