<?php
require __DIR__ . "/../vendor/autoload.php";

// perform illuminate container
/**
 * 単純なIoCコンテナを利用する場合は、
 * 引数を与えなければilluminate/containerが利用されます
 *
 * usage illuminate container
 * @see Ytake\_TestContainer\StandardDemo
 */
$container = new \Ytake\Container\Container();

$container->bind("Ytake\_TestContainer\RepositoryInterface", "Ytake\_TestContainer\Repository");
$class = $container->make("Ytake\_TestContainer\StandardDemo");

