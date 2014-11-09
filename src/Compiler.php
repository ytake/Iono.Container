<?php
namespace Ytake\Container;

use ReflectionClass;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class Compiler
 * @package Iono\Console
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Compiler
{

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

    /** @var array  */
    protected $component;

    /**
     * @param BuilderFactory $factory
     * @param Standard $printer
     * @param ReflectionClass $reflectionClass
     * @param Container $container
     * @param array $component
     */
    public function __construct(
        BuilderFactory $factory,
        Standard $printer,
        ReflectionClass $reflectionClass,
        Container $container,
        array $component
    ) {
        $this->factory = $factory;
        $this->printer = $printer;
        $this->container = $container;
        $this->reflectionClass = $reflectionClass;
        $this->component = $component;
    }

    /**
     * @return string
     */
    public function builder()
    {
        foreach($this->reflectionClass->getTraitNames() as $trait) {
            $this->traits[] = $trait;
        }
        $parameters = $this->reflectionClass->getConstructor()->getParameters();

        $construct = $this->factory->method('__construct');
        $construct->addParam($this->factory->param("app")->setTypeHint("\\". get_class($this->container)));
        $args = [];
        $nodeName = [];
        foreach($parameters as $c) {
            if($c->getClass()) {
                $construct->addParam($this->factory->param($c->name)->setTypeHint("\\" . $c->getClass()->name));
            }
            if($c->isDefaultValueAvailable()) {
                $construct->addParam($this->factory->param($c->name)->setDefault($c->getDefaultValue()));
            }
            $args[] = new \PhpParser\Node\Arg(new \PhpParser\Node\Expr\Variable($c->name));
        }
        $construct->addStmt(
            new \PhpParser\Node\Expr\FuncCall(
                new \PhpParser\Node\Name('parent::__construct'),
                $args
            )
        );

        if(count($this->traits)) {
            foreach($this->traits as $trait) {
                $nodeName[] = new \PhpParser\Node\Name("\\" . $trait);
            }
        }
        $className = "Compiler_" . str_replace("\\", "_", $this->reflectionClass->name);

        $node = $this->factory->class($className)
            ->extend("\\" . $this->reflectionClass->name)
            ->makeFinal()
            ->addStmt(
                $this->factory->property('component')->makeProtected()->setDefault($this->component)
            )
            ->addStmt($construct);

        if(count($nodeName)) {
            $node = $node->addStmt(new TraitUse($nodeName));
        }
        $class = $node->getNode();

        $stmts = [$class];
        $this->putCompileFile($className, $stmts);
        return $className;
    }


    protected function putCompileFile($filename, $stmts)
    {
        $namespace = 'namespace Iono\\Compiler;';
        $output = "<?php\n{$namespace}\n".$this->printer->prettyPrint($stmts);
        file_put_contents(__DIR__ . "/../tmp/compile/{$filename}.php", $output);
    }
} 