<?php
namespace Iono\_TestContainer;

use Iono\Container\Configure;

class ConfigureTest extends TestCase
{
	/** @var Configure */
	protected $configure;

	public function setUp()
	{
		$this->configure = new Configure();
	}

	public function testInstance()
	{
		$this->assertInstanceOf("Iono\Container\Configure", $this->configure);
	}

	public function testConfigure()
	{
		$this->assertInstanceOf("Iono\Container\Configure", $this->configure->set(['testing' => true]));
		$this->assertSame(1, count($this->configure->all()));
		$this->assertTrue($this->configure['testing']);
		$this->assertNull($this->configure['production']);
		$this->assertTrue($this->configure->offsetExists('testing'));
		$this->assertNull($this->configure->offsetGet('production'));
		$this->assertSame(true, $this->configure->offsetGet('testing'));
		$this->configure->offsetSet('hello', 'testing');
		$this->assertSame('testing', $this->configure['hello']);
		$this->configure->offsetUnset('hello');
		$this->assertNull($this->configure->offsetGet('hello'));
	}
}

