<?php

namespace app\helpers;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'style.css'
    ];
    public $js = [
        'script.js',
//        '//code.jivosite.com/script/widget/UfISoraQy5'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\YiiAsset',
    ];
}
