<?php
$autoLoader = require_once __DIR__ . "/../vendor/autoload.php";

use Doctrine\Common\Annotations\AnnotationRegistry;
use TokenReflection\Broker;

$container = new \Ytake\Container\Container();
AnnotationRegistry::registerLoader([$autoLoader, 'loadClass']);

$broker = new Broker(new Broker\Backend\Memory());
$broker->processDirectory(__DIR__ . "/../tests/binding");
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
                $annotations = $container->getManager()->reader()->getClassAnnotations($reflectionClass);
                if(count($annotations)) {
                    foreach ($annotations as $annotation) {
                        if($annotation instanceof \Ytake\Container\Annotations\Annotation\Service) {
                            $interfaces = $reflectionClass->getInterfaceNames();
                            if(count($interfaces) != 1) {
                                throw new ErrorException("mismatch");
                            }
                            $binding[$interfaces[0]] = [
                                'binding' => $class,
                                'filename' => $namespace->getFileName()
                            ];
                        }
                    }
                }
            }
        }
    }
}
file_put_contents(__DIR__ . "/../resource/scanned.binding.php", serialize($binding));