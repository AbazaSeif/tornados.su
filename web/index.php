<?php
/**
 * @link http://zenothing.com/
 */
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
define('CONFIG', __DIR__ . '/../config');

require CONFIG . '/boot.php';
require CONFIG . '/web.php';

(new yii\web\Application($config))->run();
