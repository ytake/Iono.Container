<?php
namespace Ytake\Compiled;
final class CompilerYtake_TestContainerTestingClass extends \Ytake\_TestContainer\TestingClass
{
    public function __construct(\stdClass $class, \Ytake\_TestContainer\AnnotationRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($class);
    }
}