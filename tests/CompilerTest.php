<?php

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\Container\Compiler */
    protected $compiler;

    public function setUp()
    {
        $annotationManager = new \Ytake\Container\Annotations\AnnotationManager();
        $this->compiler = new \Ytake\Container\Compiler(
            $annotationManager->driver()->reader()
        );
    }

    public function testInstance()
    {
        $container = new \Ytake\Container\Container($this->compiler);
        $container->make("Hello");
    }

}
