<?php
namespace Ytake\Container\Annotation\Annotations;

/**
 * Class Annotation
 * @package Ytake\Container\Annotation\Annotations
 */
abstract class Annotation
{

    /** @var null  */
    public $value = null;

    /** @var bool  */
    public $required = true;

} 