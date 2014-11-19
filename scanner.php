<?php
/**
 * Annotation Scanner for CLI only
 */
use Iono\Container\Annotation\Scanner;
use Iono\Container\Annotation\Resolver;

$autoLoader = require_once __DIR__ . "/vendor/autoload.php";

/** @var  $outputPath */
$outputPath = null;

$targetDir = __DIR__ . "/tests/Resolve";

/** simple annotation */
$annotation = new \Iono\Container\Annotation\AnnotationManager();
$compiler = new \Iono\Container\Compiler($annotation->reader());
$compiler->setCompilePath($outputPath)->setForceCompile(true);

$annotationFinder = new Scanner(new Resolver, $compiler);
$files = $annotationFinder->setUpScanner($autoLoader, $targetDir);
$annotationFinder->scan($files);