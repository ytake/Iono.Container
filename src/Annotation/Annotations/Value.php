<?php
namespace Ytake\Container\Annotations\Annotation;

use Ytake\Container\Annotation\Annotations\Annotation;

/**
 * Class Scope
 * @Annotation
 * @Target("PROPERTY")
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Value extends Annotation
{

    /** @var string */
    public $name;

} 