<?php
namespace Ytake\_TestContainer\Annotations;

use Ytake\Container\Annotation\FileReader;

class FileCacheReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileReader  */
    protected $reader;

    public function setUp()
    {
        $this->reader = new FileReader(".");
    }

    public function testInstance()
    {
        $this->assertInstanceOf("Ytake\Container\Annotation\FileReader", $this->reader);
    }

    public function testGetter()
    {
        $this->assertInstanceOf("Doctrine\Common\Annotations\FileCacheReader", $this->reader->getReader());
    }
} 