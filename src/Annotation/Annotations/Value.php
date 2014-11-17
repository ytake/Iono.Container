<?php
namespace Ytake\Container\Annotation\Annotations;

/**
 * Class Scope
 * @Annotation
 * @Target("PROPERTY")
 * @final
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Value extends Annotation
{

    /** @var string */
    public $value;

} 