<?php
/**
 * @link http://zenothing.com/
 */

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */


$this->title = $model->name;
$manager = !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager();
if ($manager) {
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

if ($manager) {
    $columns[] = [
        'attribute' => 'status',
        'value' => User::statuses()[$model->status]
    ];
}
?>
<div class="user-view middle">
    <div class="row">
        <div id="cabinet-controls">
            <h1><?= Html::encode($this->title) ?></h1>
            <?php
            $buttons = [];
            $buttons[] = Html::a(Yii::t('app', 'Journal'),
                ['journal/index', 'user' => $model->name], ['class' => 'btn btn-primary']);
            $buttons[] = Html::a(Yii::t('app', 'Investment'),
                ['matrix/invest', 'user' => $model->name], ['class' => 'btn btn-primary']);
            $buttons[] = Html::a(Yii::t('app', 'Income'),
                ['matrix/income', 'user' => $model->name], ['class' => 'btn btn-primary']);
            if ($model->name == Yii::$app->user->identity->name || Yii::$app->user->identity->isAdmin()) {
                $buttons[] = Html::a(Yii::t('app', 'Change Password'),
                    ['password', 'name' => $model->name], ['class' => 'btn btn-warning']);
                $buttons[] = Html::a(Yii::t('app', 'Update'),
                    ['update', 'name' => $model->name], ['class' => 'btn btn-primary']);
            }

            if ($model->name == Yii::$app->user->identity->name) {
                $buttons[] = Html::a(Yii::t('app', 'Sponsors'),
                    ['index'], ['class' => 'btn btn-primary']);
            }

            if (Yii::$app->user->identity->isAdmin()) {
                if (empty($model->hash)) {
                    $buttons[] = Html::a(Yii::t('app', 'Activate'),
                        ['email', 'code' => $model->name], ['class' => 'btn btn-primary']);
                }
                $buttons[] = Html::a(Yii::t('app', 'Delete'), ['delete', 'name' => $model->name
                ], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]);
            }

            if ($model->isManager()) {
                $buttons[] = Html::a(Yii::t('app', 'Accounts'), ['account'], ['class' => 'btn btn-primary']);
            }

            echo implode("\t", $buttons);
            ?>
        </div>

        <div>
            <div class="form-group">
                <?php
                $referral = ['user/signup', 'ref_name' => $model->name];
                echo Html::a(Yii::t('app', 'Referral Link'), $referral, ['class' => 'form-label']);
                ?>

                <input class="form-control" value="<?= Url::to($referral, true); ?>">
            </div>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => $columns,
            ]) ?>
        </div>
    </div>

</div>
