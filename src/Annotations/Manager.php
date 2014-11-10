<?php
namespace Ytake\Container\Annotations;

use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class Manager
 * @package Ytake\Container\Annotations
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Manager
{

    protected $reader = "apc";

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