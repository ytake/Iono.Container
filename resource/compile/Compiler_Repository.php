<?php
namespace Ytake\Compiled;
final class Compiler_Repository extends \Repository
{
    public function __construct(\Ytake\Container\Container $app, \stdClass $class, $string = 'testing')
    {
        parent::__construct($class, $string);
    }
}