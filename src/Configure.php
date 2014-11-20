<?php
namespace Iono\Container;

use ArrayAccess;

/**
 * Class Configure
 * @package Iono\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Configure implements ConfigureInterface, ArrayAccess
{

	/** @var array  */
	protected $configure = [];

	/**
	 * @param array $configure
	 * @return $this
	 */
	public function set(array $configure)
	{
		$this->configure = $configure;
		return $this;
	}

	/**
	 * @return array
	 */
	public function all()
	{
		return $this->configure;
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->configure[$offset]);
	}

	/**
	 * @param mixed $offset
	 * @return mixed|null
	 */
	public function offsetGet($offset)
	{
		return isset($this->configure[$offset]) ? $this->configure[$offset] : null;
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			$this->configure[] = $value;
		} else {
			$this->configure[$offset] = $value;
		}
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->configure[$offset]);
	}
}
