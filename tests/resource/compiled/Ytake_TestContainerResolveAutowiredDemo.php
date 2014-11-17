<?php
final class Ytake_TestContainerResolveAutowiredDemo extends \Ytake\_TestContainer\Resolve\AutowiredDemo
{
    public function __construct(\Ytake\_TestContainer\Resolve\RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}