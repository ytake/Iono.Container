<?php
namespace Ytake\Container\Annotations\Annotation;

/**
 * Class Scope
 * @Annotation
 * @Target("CLASS")
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Scope
{

    /** @var string chose instance "prototype", "singleton"*/
    public $scope = "prototype";

} 