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
        4 тарифных плана для потенциальных клиентов нашего проекта.
        Маркетинг план состоит из 3 активных планов для заработка в нашем проекте';

$this->registerMetaTag([
    'name' => 'description',
    'content' => $description
]);

$columns = [
    'name',
    'stake',
    'income',
    [
        'label' => 'reinvest',
        'value' => function(Type $model) {
            if ($model->reinvest) {
                return Type::get($model->reinvest);
            }
            return null;
        }
    ],
    [
        'label' => Yii::t('app', 'Action'),
        'format' => 'html',
        'value' => function(Type $model) {
            if ($model->visibility) {
                if (Yii::$app->user->isGuest) {
                    return Html::a('Open', ['/user/signup', 'type_id' => $model->id], ['class' => 'btn btn-success btn-sm']);
                } else {
                    /** @var \app\models\User $user */
                    $user = Yii::$app->user->identity;
                    if ($user->account >= $model->stake) {
                        return Html::a('Open', ['view', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']);
                    } else {
                        return Yii::t('app', 'Insufficient funds');
                    }
                }
            }
            return Yii::t('app', 'After opening Tornado');
        }
    ]
];
?>
<div class="type-index middle">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'columns' => $columns
    ]); ?>
</div>
