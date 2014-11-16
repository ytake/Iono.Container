<?php
namespace Ytake\_TestContainer;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\Container\Annotation\AnnotationManager  */
    protected $annotationReader;

    /** @var \Ytake\Container\Compiler  */
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

    public function testCompileDirectory()
    {
        $this->assertInternalType('string', $this->compiler->getCompilationDirectory());
        $this->compiler->setCompilePath(dirname(__DIR__) . '/tests/resource');
        $this->assertSame(dirname(__DIR__) . '/tests/resource/compiled', $this->compiler->getCompilationDirectory());
        $this->assertInstanceOf("Doctrine\Common\Annotations\AnnotationReader", $this->compiler->getAnnotationReader());
    }
}
