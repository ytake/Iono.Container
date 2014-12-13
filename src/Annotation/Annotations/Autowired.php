<?php
namespace Iono\Container\Annotation\Annotations;

use Iono\Container\Exception\AnnotationAutowiredException;

/**
 * @Annotation
 * @Target("PROPERTY")
 * @final
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Autowired extends Annotation
{

    /** @var  string */
    public $value;

    /** @var bool  */
    public $required = false;

    /**
     * @return null|string
     * @throws AnnotationAutowiredException
     */
    public function resolver()
    {
        $this->value = ltrim($this->value, '\\');
        if($this->required) {
            if(empty($this->value)) {
                throw new AnnotationAutowiredException("context expected", 500);
            }
            return $this->value;
        }
        return ($this->value) ? $this->value : null;
    }
}
