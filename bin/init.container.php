<?php
/**
 * initialize default directory structure
 * @author yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
$temporaryDirectory = dirname(__FILE__);
$defaultResourcePath = realpath(null) . "/resource";

echo "\033[32m[create a default directory structure]\033[0m\n";
if (!@mkdir($defaultResourcePath, 0777, true)) {
    if(file_exists($defaultResourcePath)) {
        echo "\033[31mdirectory exist {$defaultResourcePath}\033[0m\n";
    } else {
        echo "\033[31mFailed to create directory:{$defaultResourcePath}, Permission denied\033[0m\n";
    }
}

$file = file_get_contents(dirname(__FILE__) . '/data/scanner.php');
$config = file_get_contents(dirname(__FILE__) . '/data/config.php');

$config = str_replace("__CACHE_PATH__", '"' . $defaultResourcePath . '"', $config);
$config = str_replace("__SCAN_TARGET__", '"' . realpath(null) . '"', $config);

// put configure file
if(!@file_put_contents($defaultResourcePath . "/config.php", $config)) {
    echo "\033[31mFailed to make config file:{$defaultResourcePath}/config.php, Permission denied\033[0m\n";
}

// replace default configure file path
$output = str_replace("__CONFIGURE__", '"' . $defaultResourcePath . '/config.php"', $file);
// put scanner script file
if(!@file_put_contents(dirname($defaultResourcePath) . "/scanner.php", $output)) {
    echo "\033[31mFailed to make config file:" .dirname($defaultResourcePath) ."/scanner.php, Permission denied\033[0m\n";
}
