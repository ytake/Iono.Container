<?php
namespace Ytake\Container\Annotation;

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class AnnotationManager
 * @package Ytake\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class AnnotationManager
{

    /** @var string default annotation driver */
    protected $reader = "apc";

    /** @var null  */
    protected $path = null;


    public function __construct()
    {
        $this->path = dirname(dirname(realpath(__DIR__))) . "/resource";
    }

    /**
     * choose annotation reader ["apc", "file", "simple"]
     * @param string $reader
     * @return $this
     */
    public function driver($reader = "apc")
    {
        $this->reader = $reader;
        return $this;
    }

    public function setFilePath($path = null)
    {
        $this->path = (!is_null($path)) ? $path : $this->path;
        return $this;
    }

    /**
     * @return ApcReader
     */
    public function getApcReader()
    {
        return new ApcReader();
    }

    /**
     * @return FileReader
     */
    public function getFileReader()
    {
        return new FileReader();
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function reader()
    {
        $selectedReader = "get" . ucfirst($this->reader) . "Reader";
        AnnotationRegistry::registerLoader('class_exists');
        return $this->$selectedReader()->getReader();
    }
}