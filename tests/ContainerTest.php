<?php
namespace Iono\_TestContainer;

use Iono\Container\Container;

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
        $this->assertInstanceOf("Iono\Container\Container", $this->illumianteContainer);
        $this->assertInstanceOf("Iono\Container\Container", $this->container);
    }

    public function testSetUpContainer()
    {
        $this->scanner();
        $this->container->register();
        $autowired = $this->container->newInstance('Iono\_TestContainer\Resolve\AutowiredDemo');
        $this->assertInstanceOf("Iono\_TestContainer\Resolve\AutowiredDemo", $autowired);
    }
}
