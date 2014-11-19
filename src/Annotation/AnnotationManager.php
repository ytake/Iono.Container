<?php
namespace Iono\Container\Annotation;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\FileCacheReader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class AnnotationManager
 * @package Iono\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class AnnotationManager
{

    /** @var string default annotation driver */
    protected $driver = "simple";

    /** @var null  */
    protected $path = null;

    /** @var bool  */
    protected $debug = false;

    public function __construct()
    {
        $this->path = dirname(dirname(dirname(__FILE__))) . "/resource";
    }

    /**
     * choose annotation reader ["apc", "file", "simple"]
     * @param string $driver
     * @return $this
     */
    public function driver($driver = "simple")
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param null $path
     * @return $this
     */
    public function setFilePath($path = null)
    {
        $this->path = (!is_null($path)) ? $path : $this->path;
        return $this;
    }

    /**
     * @return CachedReader
     */
    protected function getApcReader()
    {
        return new CachedReader(new AnnotationReader(), new ApcCache(), $this->debug);
    }

    /**
     * @return FileCacheReader
     */
    protected function getFileReader()
    {
        return new FileCacheReader(new AnnotationReader(), $this->path, $this->debug);
    }

    /**
     * @return AnnotationReader
     */
    protected function getSimpleReader()
    {
        return new AnnotationReader;
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function reader()
    {
        $selectedReader = "get" . ucfirst($this->driver) . "Reader";
        foreach($this->getDirectory(dirname(__FILE__) . '/Annotations') as $file) {
            AnnotationRegistry::registerFile($file);
        }
        return $this->$selectedReader();
    }

    /**
     * @param $dir
     * @return array
     */
    protected function getDirectory($dir)
    {
        $result = [];
        $scanDir = scandir($dir);
        foreach ($scanDir as $key => $value) {
            if (!in_array($value, [".",".."])) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->getDirectory($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $dir . DIRECTORY_SEPARATOR . $value;
                }
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        return $this->path;
    }
}