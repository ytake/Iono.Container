<?php
namespace Ytake\Container\Annotation;

use Doctrine\Common\Annotations\FileCacheReader;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Class FileReader
 * @package Ytake\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class FileReader extends AbstractReader
{

    /** @var bool  */
    protected $debug = false;

    protected $reader;

    /**
     * @param null $path
     */
    public function __construct($path = null)
    {
        $this->reader = new FileCacheReader(
            new AnnotationReader(),
            $path,
            $this->debug
        );
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getReader()
    {
        return $this->reader;
    }
} 