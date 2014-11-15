<?php
namespace Ytake\Container\Annotation;

use TokenReflection\Broker;
use Illuminate\Filesystem\Filesystem;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class Finder
 * @package Ytake\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 */
class Finder extends Filesystem
{

    /** @var array  */
    protected $files = [];

    /** @var \Ytake\Container\Compiler */
    private static $compiler;

    /** @var Resolver  */
    protected $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param $path
     * @param array $context
     * @return int
     */
    public function putRelationFile($path, array $context = [])
    {
        $context = "<?php return unserialize('" . serialize($context) . "');";
        return $this->put($path, $context);
    }

    /**
     * @param $loader
     * @param null $outputPath
     * @param null $targetPath
     * @param array $filters
     * @return array
     */
    public function setUpScanner($loader, $outputPath = null, $targetPath = null, array $filters = [])
    {
        $manager = new \Ytake\Container\Annotation\AnnotationManager();
        self::$compiler = new \Ytake\Container\Compiler($manager->reader());
        /** force compile */
        self::$compiler = self::$compiler->setCompilePath($outputPath)->setForceCompile(true);
        /** annotation register */
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        $broker = new Broker(new Broker\Backend\Memory());
        $broker->processDirectory($targetPath, $filters);
        return $broker->getFiles();
    }

    /**
     * @param array $files
     * @return array
     */
    public function scan(array $files)
    {
        $relations = [];
        /** @var \TokenReflection\ReflectionFile $file */
        foreach($files as $file) {
            $namespaces = $file->getNamespaces();
            foreach($namespaces as $namespace) {
                /** @var \TokenReflection\ReflectionFileNamespace $namespace */
                if(count($namespace->getClasses())) {
                    require_once $namespace->getFileName();
                    foreach($namespace->getClasses() as $class => $value) {
                        $reflectionClass = new \ReflectionClass($class);
                        /** @var array $annotations */
                        $annotations = self::$compiler->getAnnotationReader()
                            ->getClassAnnotations($reflectionClass);
                        if(count($annotations)) {
                            $relations[] = $this->resolver->classAnnotation($annotations, $reflectionClass);
                        }
                    }
                }
            }
        }
        return $this->writeRelationFile($relations);
    }

    /**
     * @param array $array
     * @return int
     */
    public function writeRelationFile(array $array)
    {
        $string = "<?php\n";
        foreach($array as $row) {
            foreach($row as $dependencies) {
                foreach($dependencies as $as => $value) {
                    // @todo annotation Scope
                    $string .= "\$this->bind(\"{$value['as']}\", \"{$value['binding']}\");\n";
                    if($value['relation']) {
                        $string .= "\$this->relations[\"{$value['as']}\"] = \"{$value['binding']}\";\n";
                    }
                }
            }
        }
        return $this->put(self::$compiler->getCompiledFile(), $string);
    }
} 