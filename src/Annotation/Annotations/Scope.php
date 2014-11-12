<?php
namespace Ytake\Container\Annotation\Annotations;

/**
 * @Annotation
 * @Target("PROPERTY")
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Scope
{

    /** @var  string */
    public $value;
}