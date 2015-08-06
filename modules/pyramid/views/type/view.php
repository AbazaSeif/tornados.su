<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $user \app\models\User */
/* @var $model \app\modules\pyramid\models\Type */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Marketing'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user->identity;
?>
<div class="type-view middle">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-group">
        <?php
        if ($user->account >= $model->stake) {
            if ($user->isManager()) {
                echo Html::a(Yii::t('app', 'Create'), ['node/create', 'id' => $model->id], ['class' => 'btn btn-primary']);
            }
            else {
                echo Html::a(Yii::t('app', 'Open'), ['open', 'id' => $model->id], [
                    'class' => 'btn btn-success',
                    'data' => ['method' => 'post']
                ]);
            }
        }
        ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'stake',
            'income',
            'reinvest',
            [
                'attribute' => 'bonus',
                'value' => $user->canChargeBonus() ? $model->bonus : Yii::t('app', 'You have no referrals')
            ]
        ],
    ]) ?>

</div>
