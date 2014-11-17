<?php
namespace Ytake\Container\Annotation\Annotations;

/**
 * Class Annotation
 * @package Ytake\Container\Annotation\Annotations
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
abstract class Annotation
{

    /** @var null  */
    public $value = null;

    /** @var bool  */
    public $required = true;

} 