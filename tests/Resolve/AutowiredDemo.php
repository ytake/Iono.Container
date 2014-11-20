<?php
namespace Iono\_TestContainer\Resolve;

use Iono\Container\Annotation\Annotations\Value;
use Iono\Container\Annotation\Annotations\Autowired;

class AutowiredDemo
{

    /**
     * @var \Iono\_TestContainer\Resolve\RepositoryInterface
     * @Autowired("\Iono\_TestContainer\Resolve\RepositoryInterface")
     */
    protected $repository;

	/**
	 * @var
	 * @Value("not")
	 */
    protected $noInject;

    public function getter()
    {
        return $this->repository;
    }
}
