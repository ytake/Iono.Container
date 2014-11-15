<?php
namespace Ytake\Container;

/**
 * Class AbstractCompiler
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
abstract class AbstractCompiler
{
    /** @var  */
    protected $factory;

    /** @var   */
    protected $printer;

    /**
     * activate file parser
     * @return void
     */
    abstract protected function activate();
} 