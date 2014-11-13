<?php
namespace Ytake\_TestContainer;

/**
 * Class StandardDemo
 * @package Ytake\_TestContainer
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