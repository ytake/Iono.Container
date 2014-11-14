<?php
require __DIR__ . "/../vendor/autoload.php";
xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);


$annotationManager = new \Ytake\Container\Annotation\AnnotationManager();
$container = new \Ytake\Container\Container(
    new \Ytake\Container\Compiler($annotationManager->driver("apc")->reader())
);

$class = $container->getBean()->make("Ytake\_TestContainer\Resolve\TestingClass");

/* end */
$data = xhprof_disable();
$xhprof_runs = new XHProfRuns_Default();
$run_id = $xhprof_runs->save_run($data, "xhprof_testing");
echo "<a href=\"/xhprof_html/\">xhprof</a>";
