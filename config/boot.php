<?php
/**
 * @link http://zenothing.com/
*/
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
define('ROOT', __DIR__ . '/..');

require(ROOT . '/vendor/autoload.php');
require(ROOT . '/vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/common.php');
require(__DIR__ . '/local.php');
