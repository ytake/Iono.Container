<?php
namespace Iono\_TestContainer\Resolve;

use Iono\Container\Annotation\Annotations\Autowired;

class TestingClass
{

    /**
     * @var \Iono\_TestContainer\Resolve\RepositoryInterface
     * @Autowired("Iono\_TestContainer\Resolve\RepositoryInterface")
     */
    protected $repository;

    protected $property = null;

    protected $class;

    public function __construct(\stdClass $class)
    {
        $this->class = $class;
    }

    public function get()
    {
        return $this->repository;
    }

}
