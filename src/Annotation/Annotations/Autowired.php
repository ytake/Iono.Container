<?php
namespace Iono\Container\Annotation\Annotations;

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
     * @throws \ErrorException
     */
    public function resolver()
    {
        $this->value = ltrim($this->value, '\\');
        if($this->required) {
            if(!($this->value) ? $this->value : null) {
                throw new \ErrorException();
            }
            return $this->value;
        }
        return ($this->value) ? $this->value : null;
    }
}