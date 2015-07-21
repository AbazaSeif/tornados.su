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
        <div>
            <span>У</span>НИКАЛЬНЫЙ,
            <span>Л</span>ЕГКИЙ И
            <span>И</span>ННОВАЦИОННЫЙ МАРКЕТИНГ
        </div>
        <div>
            Попробуй работать в команде
            и ты обречен на успех!
        </div>
        <div>
            <div>Взвешенное сочетание двух формул успешного бизнеса</div>
            <div>Поодиночке мы слабы, объединившись - можем свернуть горы</div>
            <div>Вместе с командой DIAMOND RUSH</div>
        </div>
    </div>
    <div>
        <div class="interesting">
            <div>Мы заинтересованы в успехе каждого!!!</div>
            <div>
                <?= Html::a(Yii::t('app', 'Read more'), ['matrix/plan']) ?>
            </div>
            <div></div>
        </div>
        <div class="row">
            <div class="block">
                <div class="welcome">
                    <img src="/img/success-plant.jpg">
                    <div>
                        <h2>Добро пожаловать в команду!</h2>
                        <div>Вы хотите добиться успеха в жизни, стать благополучным и обеспеченным человеком?
                            Сегодня существует уникальная возможность для достижения
                            ТВОЕЙ цели — это передовой международный проект</div>
                    </div>
                </div>
                <div class="klondike">
                    DIAMOND RUSH - это  проект, направленный на получение постоянной прибыли.
                    У нас не будет "последних", а совсем наоборот, первые впоследствии помогают последующим за счет
                    ОБРАЗОВАНИЯ ФИНАНСОВОЙ ПОДУШКИ. Маркетинг очень легок и прост.
                    В нашем проекте никто не останется без денег!!!
                    Вступая в наши ряды, вы получаете множество инструментов для ведения СОБСТВЕННОГО бизнеса в сети,
                    подробные инструкции, а главное — всестороннюю поддержку членов нашей команды.
                </div>
                <h3>Быстрый старт:</h3>
                <ul class="features left">
                    <li>Сочетание лучших маркетингов</li>
                    <li>Командная работа</li>
                </ul>
                <ul class="features right">
                    <li>Постоянный заработок от 90$ до 20000$</li>
                    <li>Нет квалификаций</li>
                </ul>
                <div>
                    <?= Html::a(Yii::t('app', 'Join our team'), ['user/signup'], ['class' => 'button']) ?>
                </div>
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
    </div>
</div>
