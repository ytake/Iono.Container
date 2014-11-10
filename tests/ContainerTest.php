<?php

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
        $this->container->bind("RepositoryInterface", "Repository");
        $this->container->bind("abc", "stdClass");
        $this->assertInstanceOf("Repository", $this->container->make("RepositoryInterface"));
    }
}

interface RepositoryInterface
{
    public function get();
}

class Repository implements RepositoryInterface
{

    public function __construct(\stdClass $class, $string = "testing")
    {

    }

    public function get()
    {
        return $this;
    }

}