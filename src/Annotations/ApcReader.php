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
class ApcReader implements ReaderInterface
{

    /** @var bool  */
    protected $debug = false;

    /** @var CachedReader  */
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
     * @return CachedReader
     */
    public function getReader()
    {
        return $this->reader;
    }
} 