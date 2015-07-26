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

$columns = [
    [
        'attribute' => 'id',
        'label' => Yii::t('app', 'Name'),
        'format' => 'html',
        'value' => function($model) {
            return $model->name;
        }
    ],
    [
        'attribute' => 'stake',
        'value' => function(Type $model) {
            return $model->stake ? '$' . ((int) $model->stake) : '';
        }
    ],
    [
        'attribute' => 'income',
        'value' => function(Type $model) {
            return $model->income ? '$' . ((int) $model->income) : '';
        }
    ]
];

if (!Yii::$app->user->isGuest) {
    $columns[] = [
        'label' => Yii::t('app', 'Action'),
        'format' => 'html',
        'value' => function($model) {
            if(Yii::$app->user->identity->account >= $model->stake) {
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

    <div class="form-group">Для удобства работы в нашем проекте, администрация проекта решила создать
        три тарифных плана для  потенциальных клиентов нашего проекта.
        Маркетинг план состоит из 3 активных планов для заработка в нашем проекте</div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'columns' => $columns
    ]); ?>

    <?= Yii::t('app', "After two users signup and open a plan you'll receive reward") ?>
</div>
