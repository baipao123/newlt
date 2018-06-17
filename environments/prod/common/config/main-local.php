<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
        'cache'  => [
            'class'     => '\yii\caching\MemCache',
            "keyPrefix" => "DK-",
            "servers"   => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                ],
            ],
        ],
        'redis' => [
            'class'    => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port'     => 6379,
            'database' => 1,
        ],
    ],
];
