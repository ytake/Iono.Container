<?php
namespace Ytake\_TestContainer;

use Ytake\Container\Annotation\AnnotationManager;
use Ytake\Container\Compiler;


class AnnotationContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\Container\Container */
    protected $container;

    public function setUp()
    {
        $annotationManager = new AnnotationManager();

        $this->container = new \Ytake\Container\Container(
            new Compiler($annotationManager->driver("apc")->reader())
        );
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\Container\Container", $this->container);
    }

    public function testBinder()
    {
        /** @var TestingClass $class */
        $class = $this->container->getBean()->make("Ytake\_TestContainer\TestingClass");
        $reflectionClass = new \ReflectionClass($class);
        $reflectionProperty = $reflectionClass->getProperty("class");
        $reflectionProperty->setAccessible(true);
        $this->assertInstanceOf("Ytake\_TestContainer\AnnotationRepository", $reflectionProperty->getValue($class));
        $this->assertInstanceOf("Ytake\_TestContainer\AnnotationRepository", $class->get());
    }
}