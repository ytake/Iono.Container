<?php
namespace Ytake\_TestContainer;

use Ytake\Container\Container;

class ContainerTest extends TestCase
{
    /** @var Container */
    protected $illumianteContainer;
    /** @var Container  */
    protected $container;

    public function setUp()
    {
        parent::setUp();
        $this->illumianteContainer = new Container();
        $this->container = new Container($this->compiler);

    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\Container\Container", $this->illumianteContainer);
        $this->assertInstanceOf("Ytake\Container\Container", $this->container);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetUpContainerException()
    {
        $this->container->setContainer();
    }

    public function testSetUpContainer()
    {
        $this->scanner();
        $this->container->setContainer();
        $this->assertInstanceOf(
            "Ytake_TestContainerResolveAutowiredDemo",
            $this->container->make('Ytake\_TestContainer\Resolve\AutowiredDemo')
        );
    }
}
