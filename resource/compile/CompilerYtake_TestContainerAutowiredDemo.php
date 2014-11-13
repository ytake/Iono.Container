<?php
namespace Ytake\Compiled;
final class CompilerYtake_TestContainerAutowiredDemo extends \Ytake\_TestContainer\AutowiredDemo
{
    public function __construct(\Ytake\Compiled\CompilerYtake_TestContainerRepository $repository)
    {
        $this->repository = $repository;
    }
}