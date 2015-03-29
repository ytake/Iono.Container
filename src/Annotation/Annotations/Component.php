<?php
namespace Iono\Container\Annotation\Annotations;

use Iono\Container\Exception\AnnotationComponentException;

/**
 * @Annotation
 * @Target("CLASS")
 * @final
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Component extends Annotation
{

    /** @var string  */
    public $value;

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string $scope
     * @return array
     * @throws AnnotationComponentException
     */
    public function resolver(\ReflectionClass $reflectionClass, $scope = "prototype")
    {
        // implements interfaces
        $interfaces = $reflectionClass->getInterfaceNames();
        $scope = ($scope === "singleton") ? true : false;
        if(!count($interfaces)) {
            if(!$this->value) {
                throw new AnnotationComponentException("mismatch");
            }
            return [
                $this->value => [
                    'binding' => $reflectionClass->getName(),
                    'as' => $this->value,
                    'scope' => $scope,
                    'map' => $reflectionClass->getName()
                ]
            ];
        }
        return [
            $interfaces[0] => [
                'binding' => $reflectionClass->getName(),
                'as' => $interfaces[0],
                'scope' => $scope,
                'map' => null
            ]
        ];
    }
}
