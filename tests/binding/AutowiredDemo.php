<?php
namespace Ytake\_TestContainer;

use Ytake\Container\Annotation\Annotations\Autowired;

class AutowiredDemo
{

    /**
     * @var \Ytake\_TestContainer\RepositoryInterface
     * @Autowired("Ytake\_TestContainer\RepositoryInterface")
     */
    protected $repository;

    public function getter()
    {
        return $this->repository;
    }
}