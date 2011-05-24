<?php

require_once __DIR__.'/vendor/symfony/src/Symfony/Components/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony'          => __DIR__.'/vendor/symfony/src',
    'EasyPdf'          => __DIR__.'/src',
));

$loader->registerPrefixes(array(
    'Twig_' => __DIR__.'/vendor/twig/lib',
));

$loader->register();