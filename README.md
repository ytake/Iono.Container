Iono.Container
==================

## About
illuminate/containerを拡張したSpring Frameworkスタイルのコンテナライブラリです  
Iono.Container is a PHP Service Container(with Annotation)  
like Spring Framework Style Annotations(java)  
[![Build Status](http://img.shields.io/travis/ytake/Iono.Container/develop.svg?style=flat)](https://travis-ci.org/ytake/Iono.Container)
[![Coverage Status](http://img.shields.io/coveralls/ytake/Iono.Container/develop.svg?style=flat)](https://coveralls.io/r/ytake/Iono.Container?branch=develop)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/Iono.Container.svg?style=flat)](https://scrutinizer-ci.com/g/ytake/Iono.Container/?branch=develop)
[![Dependency Status](https://www.versioneye.com/user/projects/546a19dca760cea242000031/badge.svg?style=flat)](https://www.versioneye.com/user/projects/546a19dca760cea242000031)
![Iono.Container](http://img.shields.io/badge/iono-container-yellowgreen.svg?style=flat)  
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/acce7f34-8125-45d0-8369-5107e01b42c7/mini.png)](https://insight.sensiolabs.com/projects/acce7f34-8125-45d0-8369-5107e01b42c7)
## install
`composer.json`
```json
    "require": {
        "php": ">=5.4",
        "ext-tokenizer": "*",
        "iono/container": "0.*"
    },
```
## Usage
### initailize はじめに
#### step1
make directory structure  
初期設定関連のファイルを作成します
```bash
$ php vendor/bin/init.container.php 
```
default structure  
```yaml
project root
  - resource(configure file, annotaion scanned file, cache and compiled directory)
  - scanner.php(cli)
```
#### step2 configuration
auto make config  
自動でデフォルトのフォルダ構成を設定ファイルとして書き出します
```php
    // annotation file cache directory, and field injection cache  file only)
    'cache.path' => "/path/to/project/resource",

    // @Component, @Scope annotation scan target directory
    'scan.target.path' => "/path/to/project",

    // doctrine/annotation cache driver("file", "apc", "simple"(no cache))
    'annotation.cache.driver' => 'file',
```
Edit resource/config.php and change the your application location.  
必要に応じてディレクトリなどを変更してください
#### step3 set up container

```php
$annotation = new \Iono\Container\Annotation\AnnotationManager();

$config = new Configure();
$config->set(require dirname(dirname(__FILE__)) . '/resource/config.php');

$container = new \Iono\Container\Container(
    new \Iono\Container\Compiler($annotation->driver('file'), $config)
);
$container->register();
```
**Lightweight container(illuminate/container)**  
can not be used annotation  
コンパイラ、アノテーションを利用しない場合は軽量コンテナとして利用できます  
(illuminate/contaier extend)  
illuminate/contaierを継承していますので、利用しない場合はilluminate/contaierが利用されます
```php
$container = new \Iono\Container\Container();
```
**Laravel's container** [illuminate/container](https://github.com/illuminate/container)
#### step4 field injection
like spring framework"s annotation
javaのspring frameworkスタイルでアノテーションを利用します  

##### added Component
Componentアノテーションで自動スキャンで登録させるクラスを指定します  
use @Component Annotation
```php
namespace Acme\Container;

use Iono\Container\Annotation\Annotations\Component;

/**
 * Class Repository
 * @package Acme\Container
 * @Component("repository")
 */
class Repository
{

}
```
##### auto scan
自動スキャンさせるクラスは基本的にどこにあっても構いません
```bash
$ php scanner.php
```
put resource/scanned.binding.php file  
resource/scanned.binding.phpが生成されます  
```php
$this->bind("repository", "Acme\Container\Repository");
$this->relations["repository"] = "Acme\Container\Repository";
```
##### field injection
```php
namespace Acme;

use Iono\Container\Annotation\Annotations\Value;

/**
 * Class Perform
 * @package Acme
 */
class Perform
{

    /**
     * @Value("repository")
     */
    protected $repository;
}
```
**Acme\Perform**クラスを利用します  
```php
$container->make("Acme\Perform");
```
フィールドに指定したクラスが利用可能な状態となります  
出力されるクラスは実クラスを継承したコンパイル済みクラスが実行されます  
```bash
object(AcmePerform)#20 (1) {
  ["repository":protected]=>
  object(Acme\Container\Repository)#21 (0) {
  }
}
```
## container method

## Annotations
現在利用できるものは  
### @Component
スキャンによるコンテナへの登録  
Indicates a auto scan component  
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

### @Scope
singleton or prototype Scope annotation  
required @Component annotation (default prototype)
```php
use Ytake\Container\Annotations\Annotation\Component;

/**
 * Interface RepositoryInterface
 */
interface AnnotationRepositoryInterface {}

/**
 * @Component
 * @Scope("singleton") 
 * Class Repository
 */
class AnnotationRepository implements AnnotationRepositoryInterface {}
```

or named component annotation
```php
/**
 * @Component("hello")
 * @Scope("singleton") 
 * Class Repository
 */
class AnnotationRepository {}
```


### @Autowired
フィールドインジェクション  
annotation based field injection(Spring's own **@Autowired**)  
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
named component annotation  
クラス自体に名前をつけてコンテナに登録します
```php
/**
 * @Component("hello")
 * Class Repository
 */
class AnnotationRepository {}
```

### @Value
**@Component("name")** で登録したクラスをコンテナから取得して  
フィールドにインジェクトします
```php

class Processer
{
    /**
     * @Value('hello')
     */
    protected $hello;
}
```
**詳しくはdemoディレクトリをご覧ください**
