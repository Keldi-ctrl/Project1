<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->viewsDir,
        $config->application->formsDir,
        $config->application->pluginsDir,
        $config->application->cacheDir,
    )
)->register();

$loader->registerNamespaces([
    'Growave2\Views' => $config->application->viewsDir,
    'Growave2\Controllers' => $config->application->controllersDir,
    'Growave2\Models' => $config->application->modelsDir,
    'Growave2\Forms' => $config->application->formsDir,
    'Growave2\Plugins' => $config->application->pluginsDir,
])->register();