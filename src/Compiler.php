<?php
namespace Ytake\Container;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class Compiler
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Compiler
{

    const COMPILED_CLASS_PREFIX = "Ytake\\Compiled";

    /** @var BuilderFactory */
    protected $factory;

    /** @var Standard  */
    protected $printer;

    /** @var Container  */
    protected $container;

    /** @var ReflectionClass  */
    protected $reflectionClass;

    /** @var array  */
    protected $traits = [];

    /** @var null  */
    protected $path = null;

    /** @var bool  */
    protected $forceCompile = false;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->setCompilePath();
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return string
     */
    public function builder(ReflectionClass $reflectionClass)
    {
        $this->activate();
        $args = [];
        $nodeName = [];
        $parameters = [];
        $filedInjector = [];

        $className = $this->getClassName($reflectionClass);

        if(!$this->forceCompile) {
            if (class_exists(self::COMPILED_CLASS_PREFIX . "\\{$className}")) {
                return self::COMPILED_CLASS_PREFIX . "\\" . $className;
            }
        }

        foreach ($reflectionClass->getTraitNames() as $trait) {
            $this->traits[] = $trait;
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor) {
            $parameters = $reflectionClass->getConstructor()->getParameters();
        }

        $construct = $this->factory->method('__construct');
        // @todo
        // $construct->addParam($this->factory->param("app")->setTypeHint("\\" . get_class($this->container)));

        if($parameters) {
            foreach ($parameters as $c) {
                if ($c->getClass()) {
                    $construct->addParam($this->factory->param($c->name)->setTypeHint("\\" . $c->getClass()->name));
                }
                if ($c->isDefaultValueAvailable()) {
                    $construct->addParam($this->factory->param($c->name)->setDefault($c->getDefaultValue()));
                }
                $args[] = new \PhpParser\Node\Arg(new \PhpParser\Node\Expr\Variable($c->name));
            }
        // construct, parameter無し
        }else{
            foreach($reflectionClass as $key => $param) {
                if($key !== "name") {
                    $filedInjector["\$this->" . $key] = $key;
                    $construct->addParam($this->factory->param($key)->setTypeHint("\\" . get_class($param)));
                }
            }
        }
        if(count($filedInjector)) {

            foreach($filedInjector as $target => $inject) {
                $construct->addStmt(new \PhpParser\Node\Name("{$target} = \${$inject};"));
            }
        }
        if ($constructor) {
            $construct->addStmt(
                new \PhpParser\Node\Expr\FuncCall(
                    new \PhpParser\Node\Name('parent::__construct'),
                    $args
                )
            );
        }
        if(count($this->traits)) {
            foreach($this->traits as $trait) {
                $nodeName[] = new \PhpParser\Node\Name("\\" . $trait);
            }
        }

        $node = $this->factory->class($className)
            ->extend("\\" . $reflectionClass->name)
            ->makeFinal()
            ->addStmt($construct);

        if(count($nodeName)) {
            $node = $node->addStmt(new TraitUse($nodeName));
        }
        $class = $node->getNode();
        $stmts = [$class];
        $this->putCompileFile($className, $stmts);
        if (!class_exists(self::COMPILED_CLASS_PREFIX . "\\{$className}")) {
            require_once $this->path . "/compile/{$className}.php";
        }
        return self::COMPILED_CLASS_PREFIX . "\\" . $className;
    }

    /**
     * @param $filename
     * @param $stmts
     * @return bool
     */
    protected function putCompileFile($filename, $stmts)
    {
        $namespace = 'namespace ' . self::COMPILED_CLASS_PREFIX. ';';
        $path = $this->path . "/compile/{$filename}.php";
        $output = "<?php\n{$namespace}\n".$this->printer->prettyPrint($stmts);
        file_put_contents($path, $output);
        if(!file_exists($path)) {
            $this->putCompileFile($filename, $stmts);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return string
     */
    protected function getClassName(ReflectionClass $reflectionClass)
    {
        return "Compiler" . str_replace("\\", "", $reflectionClass->name);
    }

    /**
     * @param $path
     * @return $this
     */
    public function compilePath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompilePath()
    {
        $this->path = (is_null($this->path)) ? dirname(realpath(__DIR__)) . "/resource" : $this->path;
        return $this->path;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setCompilePath($path = null)
    {
        $this->path = (is_null($path)) ? dirname(realpath(__DIR__)) . "/resource" : $path;
        return $this;
    }

    /**
     *
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
} 