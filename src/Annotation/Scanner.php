<?php
namespace Ytake\Container\Annotation;

use TokenReflection\Broker;
use Illuminate\Filesystem\Filesystem;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Ytake\Container\CompilerInterface;

/**
 * Class Scanner
 * @package Ytake\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Scanner extends Filesystem
{

    /** @var \Ytake\Container\Compiler */
    protected $compiler;

    /** @var Resolver  */
    protected $resolver;

    /**
     * @param Resolver $resolver
     */
    public function __construct(Resolver $resolver, CompilerInterface $compiler)
    {
        $this->resolver = $resolver;
        $this->compiler = $compiler;
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
     * @param null $targetPath
     * @param array $filters
     * @return array
     */
    public function setUpScanner($loader, $targetPath = null, array $filters = [])
    {
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
                        $annotations = $this->compiler->getAnnotationReader()
                            ->getClassAnnotations($reflectionClass);
                        if(count($annotations)) {
                            $relations[] = $this->resolver->classAnnotation($annotations, $reflectionClass);
                        }
                    }
                }
            }
        }
        $this->writeRelationFile($relations);
        $this->makeDir();
        return;
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
        return $this->put($this->compiler->getCompiledFile(), $string);
    }

    /**
     * @return bool
     */
    protected function makeDir()
    {
        if(!file_exists($this->compiler->getCompilationDirectory())) {
            echo "\032[0;31m[make directory:{$this->compiler->getCompilationDirectory()}]\032[0m";
            return mkdir($this->compiler->getCompilationDirectory());
        }
        echo "\033[0;31mError [directory exists:{$this->compiler->getCompilationDirectory()}]\033[0m\n";
    }
} 