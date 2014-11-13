<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * 利用する環境に合わせたannotation readerを選択して(現在はapcのみ)
 * compilerを引数に加えると、java SpringFrameworkの
 * Componentアノテーション, Autowiredアノテーションが利用できます
 * usage illuminate container
 * @see Ytake\_TestContainer\AutowiredDemo 実行クラス
 * @see Ytake\_TestContainer\Repository Component登録されたクラス
 */
$annotation = new \Ytake\Container\Annotation\AnnotationManager();
$compiler = new \Ytake\Container\Compiler($annotation->driver("apc")->reader());

/** @var Ytake\Container\Container $compilerContainer */
$compilerContainer = new \Ytake\Container\Container($compiler);
$class = $compilerContainer->getBean()->make("Ytake\_TestContainer\AutowiredDemo");

var_dump($class->getter());
/**
 * はじめにbin/scanner.phpを実行してください
 * Component が記述されたクラスを保存します
 */