<?php
/**
 * @link http://zenothing.com/
*/

use app\modules\invoice\models\Invoice;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\invoice\models\Invoice */

$this->title = $model;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-group">
        <?php if (Yii::$app->user->identity->isManager()): ?>
            <?php
            if ('success' != $model->status) {
                echo Html::a(Yii::t('app', 'Withdraw'), ['withdraw', 'id' => $model->id], ['class' => 'btn btn-warning']);
            }
            ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php
        elseif ('success' != $model->status && $model->amount > 0):
            echo Html::beginForm('https://perfectmoney.is/api/step1.asp', 'POST');
            echo Html::hiddenInput('PAYEE_ACCOUNT', Yii::$app->perfect->wallet);
            echo Html::hiddenInput('PAYMENT_AMOUNT', $model->amount);
            echo Html::hiddenInput('PAYEE_NAME', Yii::$app->name);
            echo Html::hiddenInput('PAYMENT_UNITS', 'USD');
            echo Html::hiddenInput('PAYMENT_ID', $model->status);
            echo Html::hiddenInput('STATUS_URL', Url::to(['invoice/view', 'id' => $model->id], true));
            echo Html::hiddenInput('PAYMENT_URL', Url::to(['invoice/success', 'id' => $model->id], true));
            echo Html::hiddenInput('NOPAYMENT_URL', Url::to(['invoice/fail', 'id' => $model->id], true));
            echo Html::hiddenInput('BAGGAGE_FIELDS', 'USER_NAME');
            echo Html::hiddenInput('USER_NAME', Yii::$app->user->identity->name);
            echo Html::button(Yii::t('app', 'Pay'), [
                'name' => 'PAYMENT_METHOD',
                'type' => 'submit',
                'class' => 'btn btn-success'
            ]);
            echo Html::endForm();
        endif;
        ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'user_name',
                'format' => 'html',
                'value' => Html::a($model->user_name, ['user/view', 'name' => $model->user_name])
            ],
            [
                'attribute' => 'amount',
                'value' => '$' . abs($model->amount)
            ],
            [
                'attribute' => 'status',
                'value' => Yii::t('app', Invoice::$statuses[$model->status])
            ]
        ],
    ]) ?>

</div>
