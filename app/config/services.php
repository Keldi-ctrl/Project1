<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

/*Flashing Messages*/
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;

use Phalcon\Di;
use Phalcon\Mvc\Model\Manager as ModelsManager;

/*To get the Security Plugin working you need to add some code to the app/config/services.php file.
 Firstly you need to add the following three use statements at the top of
 the services.php file to allow these components to be seen.*/

use Phalcon\Mvc\Dispatcher as Dispatcher;
use Phalcon\Events\Manager as EventsManager;
use Growave2\Plugins\SecurityPlugin as SecurityPlugin;
use Growave2\Plugins\Auth as Auth;
use Growave2\Plugins\Myfunc as Myfunc;
use Growave2\Plugins\NotFoundPlugin as NotFoundPlugin;


$config = include __DIR__ . "/config.php";
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

$di->set('router', function(){
    return require APP_PATH . '/config/router.php';
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);

$di->set(
    "modelsManager",
    function() {
        return new ModelsManager();
    }
);
/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);



/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});
// Set up the flash service
$di->set(
    'flash',
    function () {
        return new FlashDirect();
    }
);

// Set up the flash session service
$di->set(
    'flashSession',
    function () {
        return new FlashSession();
    }
);

$di->set('partials', function() {
    $partials = new View();
    $partials->setPartialsDir('../app/views/partials');
    return $partials;
});

$di->set('auth', function(){
    return new Auth();
});
$di->set('func', function(){
    return new Myfunc();
});

$di->set('times', function(){
    return new Growave2\Plugins\Times();
});

$di->setShared('privateResources', function() {
    $pr = [];
    if (is_readable(APP_PATH . '/config/privateResources.php')) {
        $pr = include APP_PATH . '/config/privateResources.php';
    }
    return $pr;
});

$di->set('acl', function () {
    $acl = new SecurityPlugin();
    $pr = $this->getShared('privateResources')->privateResources->toArray();
    $acl->addPrivateResources($pr);
    return $acl;
});

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    // $dispatcher->setDefaultNamespace('');
    return $dispatcher;
});
