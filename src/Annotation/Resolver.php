<?php
namespace Ytake\Container\Annotation;

/**
 * Class Resolver
 * @package Ytake\Container\Annotation
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Resolver
{

    /**
     * @param array $annotations
     * @param \ReflectionClass $reflectionClass
     * @return array
     * @throws \Ytake\Container\Exception\AnnotationComponentException
     */
    public function classAnnotation(array $annotations, \ReflectionClass $reflectionClass)
    {
        $classAnnotation = [];
        foreach($annotations as $annotation) {
            if($annotation instanceof \Ytake\Container\Annotation\Annotations\Component) {
                $classAnnotation[] = $annotation->resolver($reflectionClass);
            }
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
            if($annotation instanceof \Ytake\Container\Annotation\Annotations\Autowired) {
                $propertyAnnotation[$reflectionProperty->getName()] = $annotation->resolver();
            }
            if($annotation instanceof \Ytake\Container\Annotation\Annotations\Value) {
                $propertyAnnotation[$reflectionProperty->getName()] = $annotation->value;
            }
        }
        return $propertyAnnotation;
    }
} 