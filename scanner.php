<?php
/**
 * Annotation Scanner for CLI only
 */
use Iono\Container\Annotation\Scanner;
use Iono\Container\Annotation\Resolver;

$autoLoader = require_once __DIR__ . "/vendor/autoload.php";

/** simple annotation */
$annotation = new \Iono\Container\Annotation\AnnotationManager();
$configure = new \Iono\Container\Configure();
$configure->set(require __DIR__ . "/resource/config.php");
$compiler = new \Iono\Container\Compiler($annotation, $configure);
$compiler->setForceCompile(true);

$annotationFinder = new Scanner(new Resolver, $compiler);
$files = $annotationFinder->setUpScanner($autoLoader);
$annotationFinder->scan($files);

