<?php
/**
 * @link http://zenothing.com/
 * @var string $statistics
 */

use yii\helpers\Html;

$this->title = Yii::$app->name;
$benefits = [
    'Доступный вход',
    'Никаких приглашений',
    'Моментальные выплаты'
];

$this->registerMetaTag([
    'name' => 'description',
    'content' => implode(' ⬤ ', $benefits)
]);
?>
<article class="home-index">
    <section class="cloud center">
        <div>
            <h2>
                Without good marketing,
                <small>very little will sell itself</small>
            </h2>
            <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco labori.</div>
            <?= Html::a(Yii::t('app', 'Signup'), ['/user/signup'], ['class' => 'button green']) ?>
        </div>
    </section>
    <section class="email-marketing">
        <div>
            <h2>
                Without good marketing,
                <small>very little will sell itself</small>
            </h2>
            <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco labori.</div>
            <ul>
                <li>Lorem ipsum dolor sit amet conse ctetur adipisicing</li>
                <li>Elit sed do eiusmod tempor incididunt ut</li>
                <li>Labore et dolore magna aliqua.</li>
                <li>Ut enim ad minim veniam, quis nostrud</li>
            </ul>
            <?= Html::a(Yii::t('app', 'Signup'), ['/user/signup'], ['class' => 'button']) ?>
        </div>
    </section>
    <section class="pussy">
        <div>
            <h2>
                Without good marketing,
                <small>very little will sell itself</small>
            </h2>
            <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco labori.</div>
            <ul>
                <li>Lorem ipsum dolor sit amet conse ctetur adipisicing</li>
                <li>Elit sed do eiusmod tempor incididunt ut</li>
                <li>Labore et dolore magna aliqua.</li>
                <li>Ut enim ad minim veniam, quis nostrud</li>
            </ul>
            <?= Html::a(Yii::t('app', 'Signup'), ['/user/signup'], ['class' => 'button']) ?>
        </div>
    </section>
    <section class="news">
        <div class="block-table">
            <h2>Resources</h2>
            <div>
                <div><h3>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod.</h3></div>
                <div><h3>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod.</h3></div>
                <div><h3>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod.</h3></div>
            </div>
            <div>
                <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco labori.</div>
                <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco labori.</div>
                <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco labori.</div>
            </div>
            <div>
                <div><a href="/" class="button">Read more</a></div>
                <div><a href="/" class="button">Read more</a></div>
                <div><a href="/" class="button">Read more</a></div>
            </div>
        </div>
    </section>
    <section class="rubbish">
        <div class="block-table">
            <h2><strong>What</strong> People Say?</h2>
            <div>
                <div><img src="/images/man.png"></div>
                <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim.</div>
                <div><img src="/images/woman.png"></div>
                <div>Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim.</div>
            </div>
        </div>
    </section>
</article>
