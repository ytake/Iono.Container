<?php
namespace Iono\_TestContainer\Annotations;

use Iono\Container\Annotation\AnnotationManager;

class ApcCacheReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var   */
    protected $reader;
    /** @var  AnnotationManager */
    protected $manager;
    public function setUp()
    {
        $this->manager = new AnnotationManager();
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Iono\Container\Annotation\AnnotationManager", $this->manager);
    }

    public function testApcCache()
    {
        $this->assertInstanceOf("Doctrine\Common\Annotations\CachedReader", $this->manager->driver('apc')->reader());
    }

    public function testFileCache()
    {
        $this->assertInstanceOf("Doctrine\Common\Annotations\FileCacheReader", $this->manager->driver('file')->reader());
    }

    public function testSimpleReader()
    {
        $this->assertInstanceOf("Doctrine\Common\Annotations\AnnotationReader", $this->manager->driver()->reader());
    }
} 