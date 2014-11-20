<?php
require __DIR__ . "/../vendor/autoload.php";

/**
 * 利用する環境に合わせたannotation readerを選択して(現在はapcのみ)
 * compilerを引数に加えると、java SpringFrameworkの
 * Componentアノテーション, Autowiredアノテーションが利用できます
 * usage illuminate container
 * @see Iono\_TestContainer\AutowiredDemo 実行クラス
 * @see Iono\_TestContainer\Repository Component登録されたクラス
 */
$config = new \Iono\Container\Configure();
$compiler = new \Iono\Container\Compiler(
	new \Iono\Container\Annotation\AnnotationManager(),
	$config->set(require dirname(__FILE__) . '/../resource/config.php')
);
/** @var Iono\Container\Container $compilerContainer */
$compilerContainer = new \Iono\Container\Container($compiler);
$class = $compilerContainer->setContainer()->make("Iono\_TestContainer\Resolve\AutowiredDemo");

$class->getter();
/**
 * はじめにbin/scanner.phpを実行してください
 * Component が記述されたクラスを保存します
 */
