<?php
namespace Ytake\Container\Annotations;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;


class ApcReader
{

    /** @var bool  */
    protected $debug = true;

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