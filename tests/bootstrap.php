<?php

spl_autoload_register(function ($class)
{
    if (false !== strpos($class, 'JsonWorks\\')) {
        $path = 0 === strpos($class, 'JohnStevenson\\') ? 'src' : 'tests';
        $classFile = str_replace('\\', '/', $class).'.php';
        require dirname(__DIR__).'/'.$path.'/'.$classFile;
    }
});
