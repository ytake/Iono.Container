<?php

use Ytake\Container\Annotations\Annotation\Autowired;

class ContainerTest extends \PHPUnit_Framework_TestCase
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
        $class = $this->container->make("TestingClass");
        
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