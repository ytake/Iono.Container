<?php
namespace Iono\_TestContainer\Resolve;

use Iono\Container\Annotation\Annotations\Component;
use Iono\Container\Annotation\Annotations\Autowired;

/**
 * Class Repository
 * @package Iono\_TestContainer
 * @Component("not")
 */
class NotImplementRepository
{

    protected $test;

    /**
     * @param \stdClass $std
     * @param ConstructRepository $repository
     */
    public function __construct(\stdClass $std, ConstructRepository $repository)
    {
        $this->test = true;
        $this->std = $std;
        $this->repository = $repository;
    }

    public function get()
    {
        return $this->repository;
        // TODO: Implement get() method.
    }
}
