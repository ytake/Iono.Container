<?php
namespace Ytake\Container\Annotations;

use Ytake\Container\Container;

class NewInstance
{

    /** @var Container  */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    
} 