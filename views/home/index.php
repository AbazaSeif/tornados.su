<?php
/**
 * @link http://zenothing.com/
 */
use app\widgets\Ext;
use yii\helpers\Html;

/**
 * @var string $statistics
 */

$this->title = Yii::$app->name
?>
<div class="home-index">
    <?= Ext::stamp() ?>
    <div class="top">
        <div class="benefits">
            <div>
                <img src="/img/dominatrix.jpg" />
            </div>
            <div>
                <img src="/img/zog.jpg" />
            </div>
            <div>
                <img src="/img/dollar.jpg" />
            </div>
        </div>
        <img src="/img/asian.png" />
    </div>
    <dl>
        <dt>Платежные системы</dt>
        <dd>
            <img src="/img/perfectmoney.png" />
        </dd>
        <dt>Посещения</dt>
        <dd><?= $statistics ?></dd>
    </dl>
</div>
