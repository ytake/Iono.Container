<?php
namespace Iono\Container;

/**
 * Interface ConfigureInterface
 * @package Iono\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
interface ConfigureInterface
{

	/**
	 * @param array $configure
	 * @return $this
	 */
	public function set(array $configure);

	/**
	 * @return array
	 */
	public function all();

}
