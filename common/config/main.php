<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class'      => 'yii\log\FileTarget',
                    'levels'     => ['info','error'],
                    'logVars'    => [],
                    'categories' => ['wx'],
                    'logFile'    => '@runtime/logs/wx.log',
                ],
                [
                    'class'      => 'yii\log\FileTarget',
                    'levels'     => ['info','error'],
                    'logVars'    => [],
                    'categories' => ['aliSms'],
                    'logFile'    => '@runtime/logs/aliSms.log',
                ],
                [
                    'class'      => 'yii\log\FileTarget',
                    'levels'     => ['info','error'],
                    'logVars'    => [],
                    'categories' => ['job-daily'],
                    'logFile'    => '@runtime/logs/job-daily.log',
                ],
            ],
        ],
    ],
];
