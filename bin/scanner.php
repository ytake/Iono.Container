<?php
/**
 * under construction
 * @todo
 */
use Ytake\Container\Annotation\Finder;
use Ytake\Container\Annotation\Resolver;

$autoLoader = require_once __DIR__ . "/../vendor/autoload.php";

$annotationFinder = new Finder(new Resolver);
$files = $annotationFinder->setUpScanner($autoLoader, null, __DIR__ . "/../tests/Resolve");
$annotationFinder->scan($files);