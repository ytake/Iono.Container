<?php
namespace Iono\Container\Annotation;

use TokenReflection\Broker;
use Iono\Container\CompilerInterface;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class Scanner
 * @package Iono\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Scanner extends Filesystem
{

    /** @var \Iono\Container\CompilerInterface */
    protected $compiler;

    /** @var Resolver  */
    protected $resolver;

	/**
	 * @param Resolver $resolver
	 * @param CompilerInterface $compiler
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
     * @param array $exclude
     * @return array
     */
    public function setUpScanner($loader, array $exclude = [])
    {
        /** annotation register */
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
        $finder = new Finder();
        $broker = new Broker(new Broker\Backend\Memory());
        $finder->files()
            ->in($this->compiler->scanTargetPath())
            ->exclude($exclude);
        $files = [];
        foreach ($finder as $file) {
            $files[] = $broker->processFile($file, true);
        }
        return $files;
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
                        $annotations = $this->compiler->getAnnotationManager()
                            ->getClassAnnotations($reflectionClass);
                        if(count($annotations)) {
                            $relations[] = $this->resolver->classAnnotation($annotations, $reflectionClass);
                        }
                    }
                }
            }
        }
        if($this->writeRelationFile($relations)) {
            echo "\033[32m generated component file \033[0m\n";
            $this->makeDir();
        }
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
            foreach($row as $value) {
                if(!$value['scope']) {
                    $string .= "\$this->bind(\"{$value['as']}\", \"{$value['binding']}\");\n";
                }
                if($value['scope']) {
                    $string .= "\$this->singleton(\"{$value['as']}\", \"{$value['binding']}\");\n";
                }
                if ($value['relation']) {
                    $string .= "\$this->relations[\"{$value['as']}\"] = \"{$value['binding']}\";\n";
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
            echo "\033[32m[make directory:{$this->compiler->getCompilationDirectory()}]\033[0m\n";
            return mkdir($this->compiler->getCompilationDirectory());
        }
    }
}
