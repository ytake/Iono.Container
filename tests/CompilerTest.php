<?php
namespace Ytake\_TestContainer;

class CompilerTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->scanner();
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\Container\Compiler", $this->compiler);
    }

    public function testCompileDirectory()
    {
        $this->assertInternalType('string', $this->compiler->getCompilationDirectory());
        $this->compiler->setCompilePath(dirname(__DIR__) . '/tests/resource');
        $this->assertSame(dirname(__DIR__) . '/tests/resource/compiled', $this->compiler->getCompilationDirectory());
        $this->assertInstanceOf("Doctrine\Common\Annotations\AnnotationReader", $this->compiler->getAnnotationReader());
    }

    public function testAnnotationReader()
    {
        $this->assertInstanceOf('Doctrine\Common\Annotations\AnnotationReader', $this->compiler->getAnnotationReader());
    }

    public function testSetupForce()
    {
        $this->compiler->setForceCompile(false);
        $force = $this->getProtectProperty($this->compiler, "force");
        $this->assertFalse($force->getValue($this->compiler));
    }

    public function testActivateInstance()
    {
        $method = $this->getProtectMethod($this->compiler, 'activate');
        $method->invoke($this->compiler);
        $factoryProperty = $this->getProtectProperty($this->compiler,  'factory');
        $this->assertInstanceOf('PhpParser\BuilderFactory', $factoryProperty->getValue($this->compiler));
        $printerProperty = $this->getProtectProperty($this->compiler,  'printer');
        $this->assertInstanceOf('PhpParser\PrettyPrinter\Standard', $printerProperty->getValue($this->compiler));
    }

    public function testBuilder()
    {
        $result = $this->compiler->builder(['Ytake\_TestContainer\InstanceTest' => []]);
        $this->assertInternalType('array', $result);
        $this->assertEquals('\Ytake_TestContainerInstanceTest', $result['class']);
        $this->assertTrue(file_exists($result['file']));
    }

    public function testCompileClass()
    {
        $reflection = $this->compiler->getCompilation(['Ytake\_TestContainer\InstanceTest' => []]);
        $this->assertEquals($reflection->getName(), 'Ytake_TestContainerInstanceTest');
        $this->assertInstanceOf('ReflectionClass', $reflection);
        $this->assertInstanceOf('\Ytake_TestContainerInstanceTest', $reflection->newInstance());
        $this->compiler->setForceCompile(true);
        $reflection = $this->compiler->getCompilation(['Ytake\_TestContainer\InstanceTest' => []]);
        $this->assertEquals($reflection->getName(), 'Ytake_TestContainerInstanceTest');
        $this->assertInstanceOf('ReflectionClass', $reflection);
        $this->assertInstanceOf('\Ytake_TestContainerInstanceTest', $reflection->newInstance());
    }
}

class InstanceTest
{
    protected $property;

    public function __construct()
    {

    }
}
