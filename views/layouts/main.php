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
    <link rel="image_src" href="/img/logo.png" />
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<a id="skype" href="skype:?chat&blob=o3EBwqOq_nG09j5Hdx1Y63yGzMdHlpRO4IABdAuD6qoNB3MwbEq7yMf1VcI1YnWpD1PoCZGQC39R1MUcPU4">
    <img src="/images/skype.png" />
</a>
<div class="wrap <?= $login ?>">
    <?php
    $logo = [];
    if (Yii::$app->user->getIsGuest() || !Yii::$app->user->identity->isManager()) {
        $logo[] = Html::img('@web/images/logo.png', ['alt'=>Yii::$app->name]);
        if (Yii::$app->user->getIsGuest()) {
            $logo[] = Html::tag('span', Yii::$app->name);
        }
    }
    NavBar::begin([
        'brandLabel' => implode(' ', $logo)
    ]);

    $items = [
        ['label' => Yii::t('app', 'Home'), 'url' => ['/home/index'], 'options' => ['class' => 'hideable']],
        ['label' => Yii::t('app', 'Marketing'), 'url' => ['/pyramid/type/index']],
        ['label' => Yii::t('app', 'Feedback'), 'url' => ['/feedback/feedback/' . ($manager ? 'index' : 'create')]],
        ['label' => Yii::t('app', 'FAQ'), 'url' => ['/faq/faq/index']],
        ['label' => Yii::t('app', 'News'), 'url' => ['/article/article/index']]
    ];

    if (Yii::$app->user->getIsGuest()) {
        $items[] = ['label' => Yii::t('app', 'Signup'), 'url' => ['/user/signup']];
        $items[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/user/login']];
    }
    else {
        $items[] = ['label' => Yii::t('app', 'Income'), 'url' => ['/pyramid/income/index']];
        $items[] = ['label' => Yii::t('app', 'Investments'), 'url' => ['/pyramid/node/index']];
        $items[] = ['label' => Yii::t('app', 'Payments') , 'url' =>['/invoice/invoice/index']];
        $items[] = ['label' => Yii::t('app', 'Journal') , 'url' =>['/journal/index']];
        if ($manager) {
            $items[] = ['label' => Yii::t('app', 'Gifts'), 'url' => ['/pyramid/node/gift']];
            $items[] = ['label' => Yii::t('app', 'Accounts'), 'url' => ['/account']];
            $items[] = ['label' => Yii::t('app', 'Translation') , 'url' => ['/lang/lang/index']];
        }
        $items[] = ['label' => Yii::t('app', 'Profile'), 'url' => ['/user/view']];
        $items[] = ['label' => Yii::t('app', 'Logout'), 'url' => ['/user/logout']];
    }

    if (Yii::$app->user->getIsGuest() || !Yii::$app->user->identity->isManager()) {
        $items[] = 'ru' == Yii::$app->language
            ? ['label' => 'EN', 'url' => ['/lang/lang/choice', 'code' => 'en'], 'options' => ['title' => 'English']]
            : ['label' => 'RU', 'url' => ['/lang/lang/choice', 'code' => 'ru'], 'options' => ['title' => 'Русский']];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
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

<div class="background"></div>
<div id="linux">
    <div>
        <img src="/images/linux.png" />
    </div>
    <?= Html::tag('div', Yii::t('app', 'Welcome, Linux user. We are glad you use open source software!'), [
        'class' => 'welcome'
    ]) ?>
    <div class="glyphicon glyphicon-remove"></div>
</div>

<div id="metrika">
    <!-- Yandex.Metrika informer -->
    <a href="https://metrika.yandex.ru/stat/?id=31611918&amp;from=informer"
       target="_blank" rel="nofollow">
        <img src="https://mc.yandex.ru/informer/31611918/3_0_209FFFFF_007FFFFF_0_pageviews"
             alt="Яндекс.Метрика"
             title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" />
    </a>
    <!-- /Yandex.Metrika informer -->
</div>
<footer class="footer">
    <?= Yii::t('app', 'Developed by') ?> <a href="http://zenothing.com">zenothing.com</a>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
