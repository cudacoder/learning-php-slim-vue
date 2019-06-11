<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'renderer' => [
            'template_path' => __DIR__ . DS . '..' . DS . 'views',
        ],
        'dbPath' => __DIR__ . DS . '..' . DS . 'db.sqlite',
        'logger' => [
            'name' => 'app-ui',
            'path' => 'php://stdout',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
