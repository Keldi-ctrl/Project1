<?php

use Phalcon\Mvc\Router;

// Create the router
$router = new Router();
// Define a route

//========================= SIGNIN SIGN OUT
$router->add(
    "/logIndex",
    [
        "controller" => "Sign",
        "action"     => "index",
    ]
);

$router->add(
    "/login",
    [
        "controller" => "Sign",
        "action"     => "signIn",
    ]
);

$router->add(
    "/logout",
    [
        "controller" => "Sign",
        "action"     => "signout",
    ]
);

$router->add(
    "/change",
    [
        "controller" => "Sign",
        "action"     => "change",
    ]
);
$router->add(
    "/reset",
    [
        "controller" => "Sign",
        "action"     => "changePassword",
    ]
);

//AdminContrller ========================================

$router->add(
    "/admin",
    [
        "controller" => "admin",
        "action"     => "index",
    ]
);

$router->add(
    "/admin_panel",
    [
        "controller" => "admin",
        "action"     => "panel",
    ]
);
$router->add(
    "/admin/users",
    [
        "controller" => "admin",
        "action"     => "showAllUsers",
    ]
);
$router->add(
    "/delete_user",
    [
        "controller" => "admin",
        "action"     => "deleteUser",
    ]
);

$router->add(
    "/edit_user",
    [
        "controller" => "admin",
        "action"     => "editUser",
    ]
);
$router->add(
    "/delete",
    [
        "controller" => "admin",
        "action"     => "delete",

    ]
);

$router->add(
    "/admin/partic",
    [
        "controller" => "admin",
        "action"     => "partic",

    ]
);

$router->add(
    "admin/holiday",
    [
        "controller" => "admin",
        "action"     => "getHolidays",

    ]
);

$router->add(
    "admin/holidays",
    [
        "controller" => "admin",
        "action"     => "setHolidays",

    ]
);


//Time Tracker ===========================
$router->add(
    "/start_tracking",
    [
        "controller" => "time-tracker",
        "action"     => "startTracking",
    ]
);
$router->add(
    "/stop_tracking",
    [
        "controller" => "time-tracker",
        "action"     => "stopTracking",
    ]
);

$router->add(
    "/show",
    [
        "controller" => "time-tracker",
        "action"     => "show",
    ]
);
$router->add(
    "/hide",
    [
        "controller" => "time-tracker",
        "action"     => "hide",
    ]
);
$router->add(
    "/team",
    [
        "controller" => "time-tracker",
        "action"     => "team",
    ]
);

return $router;
