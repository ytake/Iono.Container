<?php
namespace Ytake\Container\Annotations;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Class ApcReader
 * @package Ytake\Container\Annotations
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
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