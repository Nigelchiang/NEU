<?php
/**
 * Created by PhpStorm.
 * User: Nigel
 * Date: 2016/1/4
 * Time: 9:49
 */
spl_autoload_register(function ($class) {

    if (false !== stripos($class, 'NEU')) {
        //以绝对路径来包含
        require_once __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 9)) . '.php';
    } elseif (false !== stripos($class, 'Utils')) {
        require_once __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 5)) . '.php';

    }
});