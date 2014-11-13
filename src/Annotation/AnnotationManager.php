<?php
namespace Ytake\Container\Annotation;

use Ytake\Container\Annotation\ApcReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class Manager
 * @package Ytake\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class AnnotationManager
{

    protected $reader = "apc";

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

    /**
     * @return ApcReader
     */
    public function getApcReader()
    {
        return new ApcReader();
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function reader()
    {
        $selectedReader = "get" . ucfirst($this->reader) . "Reader";
        foreach($this->getDirectory(__DIR__ . '/Annotations') as $file) {
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
            if (!in_array($value, [".", ".."])) {
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