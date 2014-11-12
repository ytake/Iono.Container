<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * basic container Performance
 *  micro benchmark
 */
$container = new \Ytake\Container\Container();

$container->bind("Ytake\_TestContainer\RepositoryInterface", "Ytake\_TestContainer\Repository");

$start = microtime(true);
for($i = 0; $i < 10; $i++) {
    $container->make("Ytake\_TestContainer\StandardDemo");
}
$end = microtime(true);
echo sprintf("%0.5f\n", ($end - $start));

/**
 * Autowired Performance
 *  micro benchmark
 */
$annotation = new \Ytake\Container\Annotation\AnnotationManager();
$compiler = new \Ytake\Container\Compiler($annotation->driver("apc")->reader());

$compilerContainer = new \Ytake\Container\Container($compiler);

$start = microtime(true);
for($i = 0; $i < 10; $i++) {
    $compilerContainer->getBean()->make("Ytake\_TestContainer\AutowiredDemo");
}
$end = microtime(true);
echo sprintf("%0.5f\n", ($end - $start));
