<?php
namespace Iono\_TestContainer;

use Iono\Container\Annotation\Resolver;
use Iono\Container\Annotation\Scanner;
use Iono\Container\Configure;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var \Iono\Container\Compiler $compiler */
    protected $compiler;

    public function setUp()
    {
        $annotation = new \Iono\Container\Annotation\AnnotationManager();
	    $config = new Configure();
	    $config->set(require dirname(__FILE__) . '/resource/config.php');
        $this->compiler = new \Iono\Container\Compiler($annotation, $config);
    }

    /**
     * generate scanned file
     */
    public function scanner()
    {
        $this->compiler->setForceCompile(true);
        $annotationFinder = new Scanner(new Resolver, $this->compiler);
        $loader = require dirname(__DIR__) . '/vendor/autoload.php';
        $files = $annotationFinder->setUpScanner($loader);
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
