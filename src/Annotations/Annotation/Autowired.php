<?php
namespace Ytake\Container\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Autowired
{

    /** @var  string */
    public $value;
}