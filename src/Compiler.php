<?php
namespace Ytake\Container;

use ReflectionClass;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\PrettyPrinter\Standard;
use Ytake\Container\Annotations\Manager as AnnotationManager;

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

    /** @var AnnotationManager  */
    protected $annotation;

    /** @var bool  */
    protected $forceCompile = true;

    /**
     * @param BuilderFactory $factory
     * @param Standard $printer
     * @param AnnotationManager $annotation
     * @param ReflectionClass $reflectionClass
     * @param Container $container
     */
    public function __construct(
        BuilderFactory $factory,
        Standard $printer,
        AnnotationManager $annotation,
        ReflectionClass $reflectionClass,
        Container $container
    ) {
        $this->factory = $factory;
        $this->printer = $printer;
        $this->annotation = $annotation;
        $this->container = $container;
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * @return string
     */
    public function builder()
    {
        $args = [];
        $nodeName = [];
        $parameters = [];
        $filedInjector = [];

        $className = $this->getClassName();

        if(!$this->forceCompile) {
            if (class_exists(self::COMPILED_CLASS_PREFIX . "\\{$className}")) {
                return self::COMPILED_CLASS_PREFIX . "\\" . $className;
            }
        }
        
        foreach ($this->reflectionClass->getTraitNames() as $trait) {
            $this->traits[] = $trait;
        }

        $constructor = $this->reflectionClass->getConstructor();
        if ($constructor) {
            $parameters = $this->reflectionClass->getConstructor()->getParameters();
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
            foreach($this->reflectionClass as $key => $param) {
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
            ->extend("\\" . $this->reflectionClass->name)
            ->makeFinal()
            ->addStmt($construct);

        if(count($nodeName)) {
            $node = $node->addStmt(new TraitUse($nodeName));
        }
        $class = $node->getNode();
        $stmts = [$class];
        $this->putCompileFile($className, $stmts);
        if (!class_exists(self::COMPILED_CLASS_PREFIX . "\\{$className}")) {
            require_once $this->container['container.base.path'] . "/compile/{$className}.php";
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
        $path = $this->container['container.base.path'] . "/compile/{$filename}.php";
        $output = "<?php\n{$namespace}\n".$this->printer->prettyPrint($stmts);
        file_put_contents($path, $output);
        if(!file_exists($path)) {
            $this->putCompileFile($filename, $stmts);
        }
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return "Compiler" . str_replace("\\", "", $this->reflectionClass->name);
    }

} 