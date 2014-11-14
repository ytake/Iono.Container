<?php
namespace Ytake\Container\Annotation\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Component extends Annotation
{

    /** @var string  */
    public $value;
}