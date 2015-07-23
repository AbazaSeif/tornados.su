<?php
/**
 * @link http://zenothing.com/
*/

//$timezone = empty($_SESSION['timezone']) ? 'Europe/Moscow' : $_SESSION['timezone'];
//date_default_timezone_set($timezone);
$config = [
    'id' => 'marafon-invest',
    'name' => 'Marafon Invest',
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
                'investment/<id:\d+>' => 'pyramid/node/index',
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
