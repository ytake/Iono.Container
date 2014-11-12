<?php
namespace Ytake\Container\Annotation;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Class ApcReader
 * @package Ytake\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class ApcReader
{

    /** @var bool  */
    protected $debug = false;

    protected $reader;

    public function __construct()
    {
        $this->reader = new CachedReader(
            new AnnotationReader(),
            new ApcCache(),
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