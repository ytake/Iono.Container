<?php
namespace Ytake\Container\Annotations;

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class Manager
 * @package Ytake\Container\Annotations
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class AnnotationManager
{

    /** @var string  annotationReader driver */
    protected $driver = "apc";

    /**
     * @param $driver
     * @return $this
     */
    public function driver($driver = "apc")
    {
        $this->driver = $driver;
        return $this;
    }

    /**
 * @return ApcReader
 */
    protected function getApcReader()
    {
        return new ApcReader();
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function reader()
    {
        $selectedReader = "get" . ucfirst($this->driver) . "Reader";
        foreach($this->getDirectory(__DIR__ . '/Annotation') as $file) {
            AnnotationRegistry::registerFile($file);
        }
        return $this->$selectedReader()->getReader();
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
}