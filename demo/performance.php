<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * basic container Performance
 *  micro benchmark
 */
$container = new \Iono\Container\Container();

$container->bind("Iono\_TestContainer\Resolve\RepositoryInterface", "Iono\_TestContainer\Resolve\Repository");

$start = microtime(true);
for($i = 0; $i < 10; $i++) {
    $container->make("Iono\_TestContainer\Resolve\StandardDemo");
}
$end = microtime(true);
echo "illuminate container\n";
echo sprintf("%0.5f\n", ($end - $start));

/**
 * Autowired Performance
 *  micro benchmark
 */
$annotation = new \Iono\Container\Annotation\AnnotationManager();
$compiler = new \Iono\Container\Compiler($annotation->driver("file")->reader());
$compiler->setForceCompile(false);
$compilerContainer = new \Iono\Container\Container($compiler);
$container = $compilerContainer->setContainer();
$start = microtime(true);
for($i = 0; $i < 10; $i++) {
    $container->make("Iono\_TestContainer\Resolve\AutowiredDemo");
}
$end = microtime(true);
echo "filed injection\n";
echo sprintf("%0.5f\n", ($end - $start));
