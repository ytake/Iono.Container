<?php
namespace Ytake\_TestContainer;

use Ytake\Container\Annotation\Annotations\Autowired;

class TestingClass
{

    /**
     * @var AnnotationRepositoryInterface
     * @Autowired("Ytake\_TestContainer\AnnotationRepositoryInterface")
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