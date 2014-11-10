<?php
namespace Ytake\Container\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Autowired
{

    /** @var  string */
    public $value;
}