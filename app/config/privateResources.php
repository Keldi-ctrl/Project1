<?php

use Phalcon\Config;
use Phalcon\Logger;

return new Config([
    'privateResources' => [
        'admin' => [
            'create',
            'panel',
            'showAllUsers',
            'particularUser',
            'searchDataByDate',
            'updateTime',
            'deleteUser',
            'delete',
            'editUserData',
            'update',
            'logout',
            'getHolidays',
            'manipulateHolidays',
            'setHolidays',

        ],
        'time-tracker' => [
            'index',
            'startTracking',
            'stopTracking',
            'show',
            'team',
        ],
        'register' => [
            'index'
        ],
    ]
]);