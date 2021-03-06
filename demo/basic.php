<?php
require __DIR__ . "/../vendor/autoload.php";

// perform illuminate container
/**
 * 単純なIoCコンテナを利用する場合は、
 * 引数を与えなければilluminate/containerが利用されます
 *
 * usage illuminate container
 * @see Iono\_TestContainer\StandardDemo
 */
$container = new \Iono\Container\Container();

$container->bind("Iono\_TestContainer\Resolve\RepositoryInterface", "Iono\_TestContainer\Resolve\Repository");
$class = $container->make("Iono\_TestContainer\Resolve\StandardDemo");

