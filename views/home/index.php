<?php
/**
 * @link http://zenothing.com/
 * @var string $statistics
 * @var yii\web\View $this
 * @var \app\modules\article\models\Article[] $news
 */

use app\modules\pyramid\models\Type;
use yii\helpers\Html;
use yii\web\JqueryAsset;

$this->title = Yii::$app->name;

$this->registerJsFile('/js/lightslider.js', ['depends' => [JqueryAsset::class]]);
$this->registerCssFile('/css/lightslider.css');

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
        <ul>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </section>
    <section class="video center">
        <div class="ipad">
            <iframe frameborder="0" src="//www.youtube.com/embed/s2tGLEd5x6M"></iframe>
            <?= Html::a(Yii::t('app', 'Signup'), ['/user/signup'], ['class' => 'button']) ?>
        </div>
    </section>
    <section class="pussy">
        <div>
            <p>
                <a href="http://perfectmoney.is" title="Perfect Money - новое поколение платежных систем Интернета">
                    <img hspace="5" src="/images/perfectmoney.jpg" />
                </a>
            </p>
            <p>Программа работает по принципу одной быстро движущейся общей очереди, 1x3 под одного встают
                три приглашенных и верхний в очереди получает выплату. Приглашения по реферальной ссылке,
                реферальная программа.</p>
            <p>Участник, после регистрации выбирает вариант входа один из четырех, может заходить на все четыре.</p>

            <p>Вход на 10 дол, после оплаты через перфект мани, партнер встает в очередь на получение 30 дол.
                После того, как под него в одну очередь зашло три человека он получает 30 дол., из них 10 дол.
                реинвест в конец очереди, 17 дол. на вывод Вашего кошелька Perfect Money.</p>

            <p>Вход на 30 дол., после оплаты через перфект мани, партнер встает в очередь на получение 90 дол.
                После того, как под него зашло три человека в одну общею очередь, он получает 90 дол.,
                из них 30 дол. реинвест в конец очереди, 50 дол. на вывод Вашего кошелька Perfect Money.</p>

            <p>Вход на 60 дол., после оплаты через перфект мани, партнер встает в очередь на получение 180 дол.
                После того, как под него в одну очередь зашло три человека он получает 180 дол., из них 60 дол.,
                реинвест в конец очереди, 100 на вывод Вашего кошелька Perfect Money.</p>

            <p>4 - программа 1x3 Вход на 100 дол., после оплаты через перфект мани, партнер встает в очередь
                на получение 300 дол. После того, как под него в общею очередь зашло три человека он получает 300 дол.,
                (на руки вывода нет) для перехода во вторую программу.</p>
            <p>2 - программа 1x3 переход 300 дол., после того как под него в одну очередь, встанет один человек,
                он получает 250 дол. на вывод, 50 админу. Встал в тройку следующий, получает 250 дол. Встал третий
                человек получает на вывод 200 дол., и 100 дол уходят реинвестом вниз очереди программы на сто.</p>
            <?= Html::a(Yii::t('app', 'Signup'), ['/user/signup'], ['class' => 'button']) ?>
        </div>
    </section>
    <section class="news center">
        <div class="block-table">
            <?php
            echo Html::tag('h2', Yii::t('app', 'News'));

            $titles = [];
            $contents = [];
            $links = [];
            foreach($news as $article) {
                $titles[] = Html::tag('div', Html::tag('h3', $article->title));
                $contents[] = Html::tag('div', substr($article, 0, 200));
                $links[] = Html::tag('div',
                    Html::a(Yii::t('app', 'Read more'),
                        ['/article/article/view', 'id' => $article->id],
                        ['class' => 'button']));
            }

            echo Html::tag('div', implode('', $titles));
            echo Html::tag('div', implode('', $contents));
            echo Html::tag('div', implode('', $links));
            ?>
        </div>
    </section>
    <section class="rubbish center">
        <div>
            <h2><strong>Что</strong> говорят Люди?</h2>
            <div class="row">
                <div>
                    <img src="/images/man.png">
                </div>
                <div>
                    <div>В компании "Денежный Торнадо" может стать любой желающий достигший совершеннолетия. Оплатив вход единоразово в одну или несколько программ, Вы становитесь полноценным участником. Доступно для ВСЕХ участников интернет - ресурса.</div>
                </div>
                <div>
                    <img src="/images/woman.png">
                </div>
                <div>
                    <div>Простой понятный и доступный маркетинг, без квалификаций, без подтверждений, нет ежемесячной абонентской платы, партнерская программа, одним словом есть ВСЕ необходимое.</div>
                </div>
            </div>
        </div>
    </section>
    <section class="plans center">
        <div>
            <h2><strong>Планы</strong> и ценообразования</h2>
            <?php
            $names = [];
            $signups = [];
            foreach(Type::all() as $type) {
                if ($type->visibility) {
                    $names[] = Html::tag('div',
                        Html::tag('div', Html::tag('div', $type->name) . Html::tag('div', '$' . $type->stake), [
                        'class' => 'inner'
                    ]));

                    $signups[] = Html::tag('div',
                        Html::tag('div', Html::a(Yii::t('app', 'Signup'), ['/user/signup', 'type_id' => $type->id], ['class' => 'button']), [
                        'class' => 'inner'
                    ]));
                }
            }
            ?>
            <div class="block-table">
                <div>
                    <?= implode("\n", $names) ?>
                </div>
                <div>
                    <?= implode("\n", $signups) ?>
                </div>
            </div>
        </div>
    </section>
    <section class="exchangers">
        <div>
            <a href="https://magneticexchange.com/?p=5301" title="Magnetic Exchange - сервис обмена Perfect Money, Neteller, Payza, Solid Trust Pay, Яндекс Денег, Payweb, RedPass и Paxum"><img src="http://ad.magneticexchange.com/ru_125_125.gif" alt="Magnetic Exchange - сервис обмена Perfect Money, Neteller, Payza, Solid Trust Pay, Яндекс Денег, Payweb, RedPass и Paxum" /></a>
            <a href="https://xchange.cc/?R=13966463495285"><img src="https://xchange.cc/banner/black/125x125.gif"></a>
        </div>
    </section>
</article>
