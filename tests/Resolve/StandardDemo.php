<?php
namespace Iono\_TestContainer\Resolve;

/**
 * Class StandardDemo
 * @package Iono\_TestContainer\Resolve
 */
class StandardDemo
{

    /** @var RepositoryInterface  */
    protected $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return RepositoryInterface
     */
    public function getter()
    {
        return $this->repository;
    }
} 