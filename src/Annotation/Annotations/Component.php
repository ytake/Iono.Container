<?php
namespace Ytake\Container\Annotation\Annotations;

use Ytake\Container\Exception\AnnotationComponentException;

/**
 * @Annotation
 * @Target("CLASS")
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Component extends Annotation
{

    /** @var string  */
    public $value;

    /**
     * @param \ReflectionClass $reflectionClass
     * @return array
     * @throws AnnotationComponentException
     */
    public function resolver(\ReflectionClass $reflectionClass)
    {
        // implements interfaces
        $interfaces = $reflectionClass->getInterfaceNames();
        if(!count($interfaces)) {
            if(!$this->value) {
                throw new AnnotationComponentException("mismatch");
            }
            return [
                $this->value => [
                    'binding' => $reflectionClass->getName(),
                    'as' => $this->value,
                    'scope' => null,
                    'relation' => $reflectionClass->getName()
                ]
            ];
        }
        return [
            $interfaces[0] => [
                'binding' => $reflectionClass->getName(),
                'as' => $interfaces[0],
                'scope' => null,
                'relation' => null
            ]
        ];
    }
}