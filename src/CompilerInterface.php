<?php
namespace Iono\Container;

/**
 * Class Compiler
 * @package Iono\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
interface CompilerInterface
{

    /**
     * @param array $array
     * @return \ReflectionClass
     */
    public function getCompilation(array $array);

    /**
     * @param array $array
     * @return array
     */
    public function builder(array $array);

    /**
     * @access private
     * @return null|string
     */
    public function getCompilationDirectory();

    /**
     * @return string compiled file path
     */
    public function getCompiledFile();

    /**
     * @param $path
     * @return $this
     */
    public function setCompilePath($path = null);

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getAnnotationManager();

    /**
     * @param bool $force
     * @return $this
     */
    public function setForceCompile($force = true);

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getPropertyCompiledFile($name);

	/**
	 * @param $file
	 * @param array $dependencies
	 * @return mixed
	 */
	public function putPropertyCompiledFile($file, array $dependencies);

	/**
	 * @return mixed
	 */
	public function scanTargetPath();
}
