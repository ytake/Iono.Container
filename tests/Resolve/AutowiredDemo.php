<?php
namespace Ytake\_TestContainer\Resolve;

use Ytake\Container\Annotation\Annotations\Autowired;

class AutowiredDemo
{

    /**
     * @var \Ytake\_TestContainer\Resolve\RepositoryInterface
     * @Autowired("Ytake\_TestContainer\Resolve\RepositoryInterface")
     */
    protected $repository;

    protected $noeInject;

    public function getter()
    {
        return $this->repository;
    }
}