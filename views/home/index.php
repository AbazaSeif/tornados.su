<?php
/**
 * @link http://zenothing.com/
 * @var string $statistics
 */

$this->title = Yii::$app->name
?>
<div class="home-index">
    <div class="top"></div>
    <div class="bottom">
        <div>
            <h2>Наши преимущества</h2>
            <ol>
                <li>Доступный вход</li>
                <li>Никаких приглашений</li>
                <li>Моментальные выплаты</li>
            </ol>
            <blockquote>
                Любое действие имеет свой риск и цену этого риска,
                точно также как и любое бездействие
            </blockquote>
        </div>

        <dl>
            <dt><?= Yii::t('app', 'Payment systems') ?></dt>
            <dd>
                <img src="/img/perfectmoney.png" />
            </dd>
            <dt><?= Yii::t('app', 'Visits') ?></dt>
            <dd><?= $statistics ?></dd>
        </dl>
    </div>
</div>
