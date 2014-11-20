<?php
namespace Iono\_TestContainer;

use Iono\Container\Annotation\AnnotationManager;
use Iono\Container\Compiler;

class AnnotationContainerTest extends TestCase
{
    /** @var \Iono\Container\Container */
    protected $container;

    public function setUp()
    {
	    parent::setUp();
        $this->container = new \Iono\Container\Container($this->compiler);
	    $this->scanner();
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Iono\Container\Container", $this->container);
    }

	/**
	 * @see \Iono\_TestContainer\Resolve\TestingClass
	 */
	public function testClassResolver()
	{
		/** @var  $class */
		$class = $this->container->setContainer()->make("Iono\_TestContainer\Resolve\TestingClass");

		$reflectionClass = new \ReflectionClass($class);
		$this->assertInstanceOf("Iono\_TestContainer\Resolve\TestingClass", $class);
		$reflectionProperty = $reflectionClass->getProperty("repository");
		$reflectionProperty->setAccessible(true);
		$this->assertInstanceOf("Iono\_TestContainer\Resolve\Repository", $reflectionProperty->getValue($class));
		$this->assertInstanceOf("Iono\_TestContainer\Resolve\Repository", $class->get());

		$reflectionProperty = $reflectionClass->getProperty("class");
		$reflectionProperty->setAccessible(true);
		$this->assertInstanceOf("stdClass", $reflectionProperty->getValue($class));

		$reflectionProperty = $reflectionClass->getProperty("property");
		$reflectionProperty->setAccessible(true);
		$this->assertNull($reflectionProperty->getValue($class));
	}


	/**
	 * @see \Iono\_TestContainer\Resolve\AutowiredDemo
	 */
    public function testAutowired()
    {
	    $wired = $this->container->setContainer()->make("Iono\_TestContainer\Resolve\AutowiredDemo");
	    $reflectionClass = new \ReflectionClass($wired);
	    $reflectionProperty = $reflectionClass->getProperty("noInject");
	    $reflectionProperty->setAccessible(true);
	    $this->assertInstanceOf("Iono\_TestContainer\Resolve\NotImplementRepository", $reflectionProperty->getValue($wired));
    }
}
