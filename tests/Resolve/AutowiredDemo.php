<?php
namespace Ytake\_TestContainer\Resolve;

use Ytake\Container\Annotation\Annotations\Autowired;
use Ytake\Container\Annotation\Annotations\Value;

class AutowiredDemo
{

    /**
     * @var \Ytake\_TestContainer\Resolve\RepositoryInterface
     * @Autowired("\Ytake\_TestContainer\Resolve\RepositoryInterface")
     */
    protected $repository;

    /**
     * @Value("not")
     */
    protected $noeInject;

    public function getter()
    {
        return $this->repository;
    }
}