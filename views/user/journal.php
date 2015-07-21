<?php
/**
 * @link http://zenothing.com/
*/

/**
 * @var app\models\Record $record
 */

use app\models\User;

$info = $record->info;
if (!$info) {
    echo '';
}
elseif (isset($info['status'])) {
    echo Yii::t('app', 'Status') . ': ' . User::statuses()[$info['status']];
}
elseif (isset($info['account'])) {
    echo Yii::t('app', 'Account') . ': ' . $info['account'];
}
else {
    $key = key($info);
    echo "$key: " . $info[$key];
}
