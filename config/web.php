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
        /* @var $user \app\models\User */
        $user = Yii::$app->user->identity;
        $event->name = 'login';
        $event->sender = $user;
        Journal::writeEvent($event);
        if ($user->timezone) {
            $_SESSION['timezone'] = $user->timezone;
        }
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
$config['modules']['feedback'] = 'app\modules\feedback\Module';
$config['modules']['internal'] = 'app\modules\internal\Module';
$config['modules']['faq'] = 'app\modules\faq\Module';
$config['modules']['article'] = 'app\modules\article\Module';
$config['bootstrap'][] = 'lang';
$config['bootstrap'][] = 'invoice';
$config['bootstrap'][] = 'pyramid';
$config['bootstrap'][] = 'feedback';
$config['bootstrap'][] = 'faq';
$config['bootstrap'][] = 'article';


if (YII_ENV_DEV) {
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['modules']['gii'] = 'yii\gii\Module';

    $config['bootstrap'][] = 'gii';
    $config['bootstrap'][] = 'debug';
    $config['bootstrap'][] = 'internal';
}
