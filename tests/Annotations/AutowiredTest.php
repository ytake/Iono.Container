<?php
namespace Iono\_TestContainer\Annotations;

use Iono\Container\Annotation\Annotations\Autowired;

class AutowiredTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Autowired */
    protected $wired;
    public function setUp()
    {
        $this->wired = new Autowired;
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Iono\Container\Annotation\Annotations\Autowired", $this->wired);
    }

    public function testResolver()
    {
        $this->wired->required = false;
        $this->wired->value = null;
        $this->assertNull($this->wired->resolver());

        $this->wired->value = 'stdClass';
        $this->assertEquals('stdClass', $this->wired->resolver());
    }

    /**
     * @expectedException \Iono\Container\Exception\AnnotationAutowiredException
     */
    public function testResolverException()
    {
        $this->wired->required = true;
        $this->wired->value = 'stdClass';
        $this->assertEquals('stdClass', $this->wired->resolver());

        $this->wired->value = null;
        $this->wired->resolver();

    }
}
