<?php
namespace Ytake\_TestContainer;


use Ytake\Container\Annotation\Resolver;
use Ytake\Container\Annotation\Scanner;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var \Ytake\Container\Compiler $compiler */
    protected $compiler;

    public function setUp()
    {
        $annotation = new \Ytake\Container\Annotation\AnnotationManager();
        $this->compiler = new \Ytake\Container\Compiler($annotation->reader());
    }

    /**
     * generate scanned file
     */
    public function scanner()
    {
        $path = dirname(realpath(null)) . '/tests';
        $this->compiler->setCompilePath($path . '/resource')->setForceCompile(false);
        $annotationFinder = new Scanner(new Resolver, $this->compiler);
        $loader = require dirname(__DIR__) . '/vendor/autoload.php';
        $files = $annotationFinder->setUpScanner($loader, $path . '/Resolve');
        ob_start();
        $annotationFinder->scan($files);
        ob_end_clean();
    }

    /**
     * @param $class
     * @param $name
     * @return \ReflectionMethod
     */
    protected function getProtectMethod($class, $name)
    {
        $class = new \ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @param $class
     * @param $name
     * @return \ReflectionProperty
     */
    protected function getProtectProperty($class, $name)
    {
        $class = new \ReflectionClass($class);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }
} 