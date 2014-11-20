<?php
namespace Iono\Container\Annotation;

/**
 * Interface ReaderInterface
 * @package Iono\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
abstract class AbstractReader
{

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    abstract public function getReader();
}
