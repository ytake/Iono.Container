<?php
namespace Ytake\_TestContainer\Resolve;

use Ytake\Container\Annotation\Annotations\Autowired;

class TestingClass
{

    /**
     * @var \Ytake\_TestContainer\Resolve\RepositoryInterface
     * @Autowired("Ytake\_TestContainer\Resolve\RepositoryInterface")
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