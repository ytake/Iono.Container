<?php
namespace Ytake\Compiled;
final class CompilerYtake_TestContainerResolveTestingClass extends \Ytake\_TestContainer\Resolve\TestingClass
{
    public function __construct(\stdClass $class, \Ytake\_TestContainer\Resolve\Repository $repository)
    {
        $this->repository = $repository;
        parent::__construct($class);
    }
}