<?php
namespace Ytake\Container;

/**
 * Class Configure
 * @package Ytake\Container
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Configure
{

    /**
     * @param $path
     * @return \Illuminate\Config\Repository
     */
    public static function registerConfigure($path)
    {
        return new \Illuminate\Config\Repository(
            new \Illuminate\Config\FileLoader(
                new \Illuminate\Filesystem\Filesystem(),
                $path
            ),
            null
        );
    }
} 