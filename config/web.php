<?php
/**
 * @link http://zenothing.com/
*/

use app\behaviors\Journal;

$config['components']['user'] = [
    'identityClass' => 'app\models\User',
    'enableAutoLogin' => true,
    'loginUrl' => ['user/login'],
    'on afterLogin' => function($event) {
        $event->name = 'login';
        $event->sender = Yii::$app->user->identity;
        Journal::writeEvent($event);
    },
    'on beforeLogout' => function($event) {
        $event->name = 'logout';
        $event->sender = Yii::$app->user->identity;
        Journal::writeEvent($event);
    },
];

$config['components']['errorHandler'] = [
    'errorAction' => 'home/error',
];

$config['components']['formatter'] = [
    'class' => 'yii\i18n\Formatter',
    'dateFormat' => 'php:d-m-Y',
    'datetimeFormat' => 'php:d-m-Y H:i',
    'timeFormat' => 'php:H:i:s',
];

$config['modules']['lang'] = 'app\modules\lang\Module';
$config['modules']['invoice'] = 'app\modules\invoice\Module';
$config['modules']['pyramid'] = 'app\modules\pyramid\Module';
$config['bootstrap'][] = 'lang';
$config['bootstrap'][] = 'invoice';
$config['bootstrap'][] = 'pyramid';


if (YII_ENV_DEV) {
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['modules']['gii'] = 'yii\gii\Module';

    $config['bootstrap'][] = 'gii';
    $config['bootstrap'][] = 'debug';
}
