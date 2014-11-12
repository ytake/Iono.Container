<?php
namespace Ytake\Compiled;
final class CompilerYtake_TestContainerTestingClass extends \Ytake\_TestContainer\TestingClass
{
    public function __construct(\Ytake\_TestContainer\AnnotationRepository $class)
    {
        $this->class = $class;
    }
}