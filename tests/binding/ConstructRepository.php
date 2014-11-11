<?php
namespace Ytake\_TestContainer;

class ConstructRepository implements RepositoryInterface
{

    protected $class;

    /**
     * @param \stdClass $class
     */
    public function __construct(\stdClass $class)
    {
        $this->class = $class;
    }

    public function get()
    {
        return $this->class;
    }

} 