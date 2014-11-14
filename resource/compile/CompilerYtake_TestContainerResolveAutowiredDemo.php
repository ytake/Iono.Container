<?php
namespace Ytake\Compiled;
final class CompilerYtake_TestContainerResolveAutowiredDemo extends \Ytake\_TestContainer\Resolve\AutowiredDemo
{
    public function __construct(\Ytake\_TestContainer\Resolve\Repository $repository)
    {
        $this->repository = $repository;
    }
}