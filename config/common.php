<?php
/**
 * @link http://zenothing.com/
*/

$config = [
    'id' => 'tornado-club',
    'name' => 'Tornado Club',
    'basePath' => __DIR__ . '/..',
    'bootstrap' => ['log'],
    'defaultRoute' => 'home/index',
    'language' => empty($_COOKIE['lang']) ? 'ru' : 'en',
//    'timeZone' => $timezone,
    'charset' => 'utf-8',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\ApcCache'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'enabled' => false
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'plan<id:\d+>/open' => 'pyramid/type/open',
                'plan<id:\d+>' => 'pyramid/type/view',
                'invoices/user/<user:[\w_\-\.]+>' => 'invoice/invoice/index',
                'investments/user/<user:[\w_\-\.]+>' => 'pyramid/node/invest',
                'investment/<id:\d+>' => 'pyramid/node/index',
                'journal/<id:\d+>' => 'journal/view',
                'feedback/template/<template:\w+>' => 'feedback/create',
                '<scenario:(withdraw|payment)>/user/<user:[\w_\-\.]+>' => 'invoice/index',
                '<scenario:(withdraw|payment)>/create' => 'invoice/create',
                '<scenario:(withdraw|payment)>' => 'invoice/index',
                'settings/<name:[\w_\-\.]+>' => 'user/update',
                'password/<name:[\w_\-\.]+>' => 'user/password',
                'reset/<code:[\w_\-]+>' => 'user/password',
                'login' => 'user/login',
                'register' => 'user/signup',
                'cabinet' => 'user/view',
                'payments' => 'invoice/invoice/index',
                'marketing' => 'pyramid/type/index',
                'investments' => 'pyramid/node/index',
                'income' => 'pyramid/income/index',
                'translations' => 'lang/lang/index',
                '/' => 'home/index',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\DbMessageSource'
                ]
            ]
        ],
        'session' => [
            'class' => 'yii\web\CacheSession',
            'name' => 'auth'
        ],
    ],
    'params' => null,
];
