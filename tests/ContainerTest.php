<?php
namespace Ytake\_TestContainer;

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
        $this->container->bind("Ytake\_TestContainer\Resolve\RepositoryInterface", "Ytake\_TestContainer\Resolve\Repository");
        $this->container->bind("abc", "stdClass");
        $this->assertInstanceOf("Ytake\_TestContainer\Resolve\Repository", $this->container->make("Ytake\_TestContainer\Resolve\RepositoryInterface"));
    }
}
