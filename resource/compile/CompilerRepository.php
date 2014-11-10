<?php
namespace Ytake\Compiled;
final class CompilerRepository extends \Repository
{
    public function __construct(\stdClass $class, $string = 'testing')
    {
        parent::__construct($class, $string);
    }
}