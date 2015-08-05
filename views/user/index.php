<?php
/**
 * @link http://zenothing.com/
*/

use app\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\User */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');

$columns = [
    [
        'attribute' => 'name',
        'format' => 'html',
        'value' => function($model) {
            return Html::a($model->name, ['view', 'name' => $model->name]);
        }
    ],
    'email:email',
    'account',
    [
        'attribute' => 'perfect',
        'label' => Yii::t('app', 'Wallet')
    ],
    'skype'
];

if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
    $columns[] = [
        'format' => 'html',
        'contentOptions' => ['class' => 'action'],
        'value' => function($model) {
            return implode(' ', [
                Html::a('', ['update', 'name' => $model->name], ['class' => 'glyphicon glyphicon-pencil']),
//                Html::a('', ['delete', 'id' => $model->id], ['class' => 'glyphicon glyphicon-trash'])
            ]);
        }
    ];
}
?>
<div class="user-index contain">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>

</div>
