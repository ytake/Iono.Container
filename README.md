Ytake.Container
==================
**develop only**
## About
illuminate/containerを拡張したコンテナライブラリです  

## 注意
現在開発中ですが、それっぽく動きます  
ただし、PHPUnitテストで動かすにはapc.enable_cliを有効にしなければ動きません  
**また現在はapcu-beta必須です**
## Usage
todo

## What?
javaのspring frameworkスタイルでアノテーションを利用します  

現在利用できるものは  
### @Component
依存の定義
```php
use Ytake\Container\Annotations\Annotation\Component;

/**
 * Interface RepositoryInterface
 */
interface AnnotationRepositoryInterface {}

/**
 * @Component
 * Class Repository
 */
class AnnotationRepository implements AnnotationRepositoryInterface {}
```

### @Autowired
プロパティによる解決
```php
class TestingClass
{

    /**
     * @Autowired("AnnotationRepositoryInterface")
     */
    protected $class;

    public function get()
    {
        return $this->class;
    }
}

```
**これらはtests配下のテストコードに含まれています**
