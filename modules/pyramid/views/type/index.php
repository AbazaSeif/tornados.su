<?php
/**
 * @link http://zenothing.com/
 */

use app\modules\pyramid\models\Type;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Plans');
$description = 'Для удобства работы в нашем проекте, администрация проекта решила создать
        три тарифных плана для потенциальных клиентов нашего проекта.
        Маркетинг план состоит из 3 активных планов для заработка в нашем проекте';

$this->registerMetaTag([
    'name' => 'description',
    'content' => $description
]);

$columns = [
    'name',
    'stake',
    'income',
    'reinvest'
];

if (!Yii::$app->user->isGuest) {
    $columns[] = [
        'label' => Yii::t('app', 'Action'),
        'format' => 'html',
        'value' => function(Type $model) {
            /** @var \app\models\User $user */
            $user = Yii::$app->user->identity;
            if ($user->account >= $model->stake) {
                return Html::a('Open', ['view', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']);
            }
            else {
                return Yii::t('app', 'Insufficient funds');
            }
        }
    ];
}
?>
<div class="type-index">
    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>

    <?= Html::tag('div', $description, ['class' => 'form-group']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'columns' => $columns
    ]); ?>

    <?= Yii::t('app', "After two users signup and open a plan you'll receive reward") ?>
</div>
