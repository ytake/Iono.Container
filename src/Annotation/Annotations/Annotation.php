<?php
namespace Ytake\Container\Annotation\Annotations;

/**
 * Class Annotation
 * @package Ytake\Container\Annotation\Annotations
 */
abstract class Annotation
{


    public $value = null;


    public $required = true;


    public $name = null;


    public $scope = null;
} 