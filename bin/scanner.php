<?php
/**
 * under construction
 * @todo
 */
$autoLoader = require_once __DIR__ . "/../vendor/autoload.php";

use TokenReflection\Broker;
use Doctrine\Common\Annotations\AnnotationRegistry;

$manager = new \Ytake\Container\Annotation\AnnotationManager();
$compiler = new \Ytake\Container\Compiler($manager->reader());
$container = new \Ytake\Container\Container(
    $compiler
);
AnnotationRegistry::registerLoader([$autoLoader, 'loadClass']);

$broker = new Broker(new Broker\Backend\Memory());
$broker->processDirectory(__DIR__ . "/../tests/Resolve");
$files = $broker->getFiles();

$binding = [];

foreach($files as $file) {
    $namespaces = $file->getNamespaces();
    foreach($namespaces as $namespace) {
        /** @var \TokenReflection\ReflectionFileNamespace $namespace */
        if(count($namespace->getClasses())) {
            require_once $namespace->getFileName();
            foreach($namespace->getClasses() as $class => $value) {
                $reflectionClass = new \ReflectionClass($class);
                $annotations = $compiler->getAnnotationReader()->getClassAnnotations($reflectionClass);
                if(count($annotations)) {
                    foreach ($annotations as $annotation) {
                        if($annotation instanceof \Ytake\Container\Annotation\Annotations\Component) {
                            // implements interfaces
                            $interfaces = $reflectionClass->getInterfaceNames();
                            if(!count($interfaces)) {
                                if(!$annotation->value) {
                                    throw new ErrorException("mismatch");
                                }
                                $binding[$annotation->value] = [
                                    'binding' => $class,
                                    'as' => $annotation->value,
                                    'scope' => null
                                ];
                            } else {
                                $binding[$interfaces[0]] = [
                                    'binding' => $class,
                                    'as' => $interfaces[0],
                                    'scope' => null
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
}
$string = null;
foreach($binding as $bind) {
    $string .= "\$this->bind(\"{$bind['as']}\", \"{$bind['binding']}\");\n";
}
file_put_contents(
    $compiler->getCompilePath() . "/scanned.binding.php", "<?php $string"
);