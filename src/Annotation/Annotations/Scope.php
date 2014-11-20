<?php
namespace Iono\Container\Annotation\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 * @final
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Scope extends Annotation
{

    /** @var string chose instance "prototype", "singleton"*/
    public $value = "prototype";

}
