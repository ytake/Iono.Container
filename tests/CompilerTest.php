<?php
namespace Ytake\_TestContainer;

class CompilerTest extends \PHPUnit_Framework_TestCase
{

    protected $annotationReader;

    protected $compiler;

    public function setUp()
    {
        $this->annotationReader = new \Ytake\Container\Annotation\AnnotationManager();
        $this->compiler = new \Ytake\Container\Compiler(
            $this->annotationReader->reader()
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\Container\Annotation\AnnotationManager", $this->annotationReader);
        $this->assertInstanceOf("Ytake\Container\Compiler", $this->compiler);
    }

}
