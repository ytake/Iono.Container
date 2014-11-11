<?php
namespace Ytake\_TestContainer;

class ParentContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Illuminate\Container\Container */
    protected $container;

    public function setUp()
{
    $this->container = new \Illuminate\Container\Container();
}

    public function testInstance()
{
    $this->assertInstanceOf("Illuminate\Container\Container", $this->container);
}

    public function testBinder()
    {
        $this->container->bind("ParentRepositoryInterface", "ParentRepository");
        $this->container->bind("abc", "stdClass");
        $this->assertInstanceOf("ParentRepository", $this->container->make("ParentRepositoryInterface"));
    }
}

interface ParentRepositoryInterface
{
    public function get();
}

class ParentRepository implements ParentRepositoryInterface
{

    public function __construct(\stdClass $class, $string = "testing")
    {

    }

    public function get()
    {
        return $this;
    }

}