<?php
/**
 * Annotation Scanner for CLI only
 */
use Ytake\Container\Annotation\Scanner;
use Ytake\Container\Annotation\Resolver;

$autoLoader = require_once __DIR__ . "/../vendor/autoload.php";

/** @var  $outputPath */
$outputPath = null;

$targetDir = __DIR__ . "/../tests/Resolve";

/** simple annotation */
$annotation = new \Ytake\Container\Annotation\AnnotationManager();
$compiler = new \Ytake\Container\Compiler($annotation->reader());
$compiler->setCompilePath($outputPath)->setForceCompile(true);

$annotationFinder = new Scanner(new Resolver, $compiler);
$files = $annotationFinder->setUpScanner($autoLoader, $targetDir);
$annotationFinder->scan($files);