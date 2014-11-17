Iono.Container
==================
**develop only**
## About
illuminate/containerを拡張したコンテナライブラリです  
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/Iono.Container.svg?style=flat)](https://scrutinizer-ci.com/g/ytake/Iono.Container/?branch=develop)
[![Dependency Status](https://www.versioneye.com/user/projects/546a19dca760cea242000031/badge.svg?style=flat)](https://www.versioneye.com/user/projects/546a19dca760cea242000031)
![Iono.Container](http://img.shields.io/badge/iono-container-yellowgreen.svg?style=flat)

## 注意
現在開発中ですが、それっぽく動きます  

## Usage
### set up annotation reader
利用用途に合わせてファイルキャッシュ、apcキャッシュ、ノンキャッシュの
アノテーションリーダーを選択します

```php
$annotation = new \Ytake\Container\Annotation\AnnotationManager();
// file cache
$annotation->driver("file")->reader();
// apc cache
$annotation->driver("apc")->reader();
// non cache
$annotation->reader();
```
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

### @Component Named
クラス自体に名前をつけてコンテナに登録します
```php
/**
 * @Component("hello")
 * Class Repository
 */
class AnnotationRepository {}
```

### @Value
**@component("name")** で登録したクラスをコンテナから取得して  
フィールドにインジェクトします
```php

class Processer 
{
    /**
     * @Value('hello')
     */
    $protected $hello;
}
```
**これらはtests配下のテストコードに含まれています**
