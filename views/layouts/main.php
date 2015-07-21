<?php
/**
 * @link http://zenothing.com/
*/
use app\widgets\Alert;
use app\helpers\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
$login = Yii::$app->user->isGuest ? '' : 'login';
$manager = !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<div class="background"></div>
<div class="navbar-border"></div>
<?php $this->beginBody() ?>
<div class="wrap <?= $login ?>">
    <header>
        <div class="brand">
            <?= Html::img('/img/runner.png') ?>
            <div>Marafon Invest</div>
        </div>
    </header>
    <?php
    NavBar::begin();

    $items = [
        ['label' => Yii::t('app', 'Home'), 'url' => ['/home/index'], 'options' => ['class' => 'hideable']],
        ['label' => Yii::t('app', 'Marketing'), 'url' => ['/pyramid/type/index']]
    ];

    if (Yii::$app->user->isGuest) {
        $items[] = ['label' => Yii::t('app', 'Signup'), 'url' => ['/user/signup']];
        $items[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/user/login']];
    }
    else {
        $items[] = ['label' => Yii::t('app', 'Income'), 'url' => ['/pyramid/income/index']];
        $items[] = ['label' => Yii::t('app', 'Investments'), 'url' => ['/pyramid/node/index']];
        $items[] = ['label' => Yii::t('app', 'Payments') , 'url' =>['/invoice/invoice/index']];
        if ($manager) {
            $items[] = ['label' => Yii::t('app', 'Translation') , 'url' => ['/lang/lang/index']];
        }
        $items[] = ['label' => Yii::t('app', 'Profile'), 'url' => ['/user/view']];
        $items[] = ['label' => Yii::t('app', 'Logout'), 'url' => ['/user/logout']];
    }

    if (Yii::$app->user->isGuest || !Yii::$app->user->identity->isManager()) {
        $items[] = empty($_COOKIE['lang'])
            ? ['label' => 'EN', 'url' => ['/lang/lang/choice', 'code' => 'en'], 'options' => ['title' => 'English']]
            : ['label' => 'RU', 'url' => ['/lang/lang/choice', 'code' => 'ru'], 'options' => ['title' => 'Русский']];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $items,
    ]);
    NavBar::end();
    ?>


    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => false,
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<div id="metrika">
    <!-- Yandex.Metrika informer -->
    <a href="https://metrika.yandex.ru/stat/?id=31493068&amp;from=informer"
       target="_blank" rel="nofollow">
        <img src="https://mc.yandex.ru/informer/31493068/3_1_FFFFFFFF_FFFFFFFF_0_pageviews"
             title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)"
             alt="Яндекс.Метрика" />
    </a>
    <!-- /Yandex.Metrika informer -->
</div>
<!--<footer class="footer">-->
<!--    <div class="container">-->
<!--    </div>-->
<!--</footer>-->

<script src="/script.js"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
