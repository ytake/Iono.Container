<?php
namespace Iono\_TestContainer\Resolve;

use Iono\Container\Annotation\Annotations\Autowired;

class AutowiredDemo
{

    /**
     * @var \Iono\_TestContainer\Resolve\RepositoryInterface
     * @Autowired("\Iono\_TestContainer\Resolve\RepositoryInterface")
     */
    protected $repository;

    protected $noeInject;

    public function getter()
    {
        return $this->repository;
    }
}
