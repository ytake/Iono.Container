<?php
/**
 * Annotation Scanner for CLI only
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
use Iono\Container\Annotation\Scanner;
use Iono\Container\Annotation\Resolver;
// Run mode
$run = (isset($argv[1])) ? $argv[1] : null;

$autoLoader = require_once __DIR__ . "/vendor/autoload.php";

/** simple annotation */
$annotation = new \Iono\Container\Annotation\AnnotationManager();
$configure = new \Iono\Container\Configure();
$configure->set(require __CONFIGURE__);
$compiler = new \Iono\Container\Compiler($annotation, $configure);
$compiler->setForceCompile(true);

switch((string) $run) {
    // remove compiled files(annotation cache(file), compiled class file, field inject cache file)
    case "remove":
        $compiler->deleteCompiledFile();
        // selected apc cache driver
        if($compiler->getAnnotationManager() instanceof \Doctrine\Common\Annotations\CachedReader) {
            // cache clear
            apc_clear_cache();
        }
        echo "\033[32m clear compiled files \033[0m\n";
        break;
    // generate scan component file
    default:
        $annotationFinder = new Scanner(new Resolver, $compiler);
        $files = $annotationFinder->setUpScanner($autoLoader);
        $annotationFinder->scan($files);
        break;
}
