<?php

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read router
     */
    include __DIR__ . "/../app/config/router.php";

    /**
     * Read auto-loader
     */

    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}
