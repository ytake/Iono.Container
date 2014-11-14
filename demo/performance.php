<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * basic container Performance
 *  micro benchmark
 */
$container = new \Ytake\Container\Container();

$container->bind("Ytake\_TestContainer\Resolve\RepositoryInterface", "Ytake\_TestContainer\Resolve\Repository");

$start = microtime(true);
for($i = 0; $i < 10; $i++) {
    $container->make("Ytake\_TestContainer\Resolve\StandardDemo");
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
$container = $compilerContainer->getBean();
$start = microtime(true);
for($i = 0; $i < 10; $i++) {
    $container->make("Ytake\_TestContainer\Resolve\AutowiredDemo");
}
$end = microtime(true);
echo sprintf("%0.5f\n", ($end - $start));
