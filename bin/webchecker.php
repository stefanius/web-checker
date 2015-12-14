<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../src/Stefanius/WebChecker/Checker/WebCheck.php');
require_once(dirname(__FILE__) . '/../src/Stefanius/WebChecker/Matchers/PlainTextMatcher.php');

$files = [];
$dir = new DirectoryIterator(dirname(__FILE__) . '/../checks');
info($dir);

foreach ($files as $filename) {
    runChecks($filename);
}

function runChecks($filename) {
    require_once($filename);
    $className = getClassName($filename);
    $class = new $className();

    $methods = get_class_methods($class);

    foreach ($methods as $method) {
        if (strpos($method, 'check') !== false) {
            echo "Running: $className::$method\n";
            call_user_func( array($class,$method));
        }
    }
}

function getClassName($filename) {
    $filename = rtrim($filename, '.php');
    $elements = explode('/', $filename);

    return end($elements);
}

function info($dir) {
    global $files;

    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
            $files[] = $fileinfo->getRealPath();
        } else if (!$fileinfo->isDot() && $fileinfo->isDir()) {
            $dir2 = new DirectoryIterator($fileinfo->getRealPath());
            info($dir2);
        }
    }
}