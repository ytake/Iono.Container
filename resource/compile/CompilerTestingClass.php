<?php
namespace Ytake\Compiled;
final class CompilerTestingClass extends \TestingClass
{
    public function __construct(\AnnotationRepository $class)
    {
        $this->class = $class;
    }
}