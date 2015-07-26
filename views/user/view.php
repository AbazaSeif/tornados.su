<?php
/**
 * @link http://zenothing.com/
*/

use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */


$this->title = $model->name;
if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isManager()) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}

$columns = [
    'name',
    'email:email',
    'account',
    'phone',
    'skype',
    'forename',
    'surname',
    'perfect',
    [
        'attribute' => 'country',
        'format' => 'html',
        'value' => $model->country ? Html::tag('span', $model->country, ['class' => 'country']) : null
    ],
    [
        'attribute' => 'timezone',
        'format' => 'html',
        'value' => Html::tag('span', $model->timezone ?: 'Europe/Moscow', ['class' => 'timezone'])
    ],
    'duration',
];

if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
    $columns[] = [
        'attribute' => 'status',
        'value' => User::statuses()[$model->status]
    ];
}
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Journal'),
            ['journal/index', 'user' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Investment'),
            ['pyramid/node/index', 'user' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Income'),
            ['pyramid/income/index', 'user' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?php
        if (!Yii::$app->user->isGuest) {
            if ($model->name == Yii::$app->user->identity->name || Yii::$app->user->identity->isAdmin()) {
                echo Html::a(Yii::t('app', 'Change Password'),
                    ['user/password', 'name' => $model->name], ['class' => 'btn btn-warning']);
            }
            ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'name' => $model->name], ['class' => 'btn btn-primary']) ?>
            <?php
            if (Yii::$app->user->identity->isAdmin()) {
                if (empty($model->hash)) {
                    echo Html::a(Yii::t('app', 'Activate'), ['email', 'code' => $model->name], ['class' => 'btn btn-primary']) . ' ';
                }
                echo Html::a(Yii::t('app', 'Delete'), ['delete', 'name' => $model->name
                ], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }
        }
        ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $columns,
    ]) ?>

</div>
