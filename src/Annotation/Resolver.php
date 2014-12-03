<?php
namespace Iono\Container\Annotation;

use Iono\Container\Annotation\Annotations\Scope;
use Iono\Container\Annotation\Annotations\Component;

/**
 * Class Resolver
 * @package Iono\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Resolver
{

    /**
     * @param array $annotations
     * @param \ReflectionClass $reflectionClass
     * @return array
     * @throws \Iono\Container\Exception\AnnotationComponentException
     */
    public function classAnnotation(array $annotations, \ReflectionClass $reflectionClass)
    {
        $classAnnotation = [];
        $component = null;
        $scope = "prototype";
        foreach($annotations as $annotation) {
            if($annotation instanceof Scope) {
                $scope = $annotation->value;
            }
            if($annotation instanceof Component) {
                $component = $annotation;
            }
        }
        if($component instanceof Component) {
            $classAnnotation = $component->resolver($reflectionClass, $scope);
        }
        return $classAnnotation;
    }

    /**
     * @param array $annotations
     * @param \ReflectionProperty $reflectionProperty
     * @return null
     * @throws \ErrorException
     */
    public function propertyAnnotation(array $annotations, \ReflectionProperty $reflectionProperty)
    {
        $propertyAnnotation = null;
        foreach($annotations as $annotation) {
            if($annotation instanceof \Iono\Container\Annotation\Annotations\Autowired) {
                $propertyAnnotation[$reflectionProperty->getName()] = $annotation->resolver();
            }
            if($annotation instanceof \Iono\Container\Annotation\Annotations\Value) {
                $propertyAnnotation[$reflectionProperty->getName()] = $annotation->value;
            }
        }
        return $propertyAnnotation;
    }
}
