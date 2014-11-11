<?php
namespace Ytake\_TestContainer;

use Ytake\Container\Annotations\Annotation\Autowired;

class AnnotationContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\Container\Container */
    protected $container;

    public function setUp()
    {
        $this->container = new \Ytake\Container\Container();
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\Container\Container", $this->container);
    }

    public function testBinder()
    {
        /** @var TestingClass $class */
        $class = $this->container->make("TestingClass");
        $reflectionClass = new \ReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty("class");
        $reflectionProperty->setAccessible(true);
        $this->assertInstanceOf("AnnotationRepository", $reflectionProperty->getValue($class));
        $this->assertInstanceOf("AnnotationRepository", $class->get());
    }
}

class TestingClass
{
    /**
     * @var
     * @Autowired("AnnotationRepositoryInterface")
     */
    protected $class;

    public function get()
    {
        return $this->class;
    }
}