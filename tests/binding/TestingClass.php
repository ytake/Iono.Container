<?php
namespace Ytake\_TestContainer;

use Ytake\Container\Annotation\Annotations\Autowired;
use Ytake\_TestContainer\AnnotationRepositoryInterface;

class TestingClass
{

    /**
     * @var AnnotationRepositoryInterface
     * @Autowired("Ytake\_TestContainer\AnnotationRepositoryInterface")
     */
    protected $class;

    public function get()
    {
        return $this->class;
    }
}