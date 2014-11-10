<?php
namespace Ytake\Compiled;
final class Compiler_TestingClass extends \TestingClass
{
    public function __construct(\Ytake\Container\Container $app, \AnnotationRepository $class)
    {
        $this->class = $class;
    }
}