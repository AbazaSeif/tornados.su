<?php
/**
 * @link http://zenothing.com/
*/

use app\modules\invoice\models\Invoice;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\Invoice */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Invoices');

$actions = ['class' => 'yii\grid\ActionColumn'];
if (!Yii::$app->user->identity->isManager()) {
    $actions['template'] = '{view}';
}

$columns = ['id'];

if (empty($_GET['user'])) {
    $columns[] = 'user_name';
}

$columns[] = [
    'attribute' => 'amount',
    'value' => function(Invoice $model) {
        return isset($_GET['scenario']) ? abs($model->amount) : $model->amount;
    }
];
$columns[] = [
    'attribute' => 'status',
    'format' => 'html',
    'value' => function(Invoice $model) {
        $status = Yii::t('app', Invoice::$statuses[$model->status]);
        if ($model->amount < 0 && 'success' != $model->status && Yii::$app->user->identity->isManager()) {
            $status .= ' ' .Html::a(Yii::t('app', 'Withdraw'),
                ['withdraw', 'id' => $model->id], ['class' => 'btn btn-warning btn-xs']);
        }
        return $status;
    }
];
$columns[] = $actions;
?>
<div class="invoice-index">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="form-group">
        <?php if (!Yii::$app->user->identity->isManager()): ?>
        <?= Html::a(Yii::t('app', 'Pay'), ['create', 'scenario' => 'payment'], ['class' => 'btn btn-success']); ?>
        <?php
        if (Yii::$app->user->identity->account > 0) {
            echo Html::a(Yii::t('app', 'Withdraw'), ['create', 'scenario' => 'withdraw'], ['class' => 'btn btn-primary']);
        }
        ?>
        <?php endif ?>
    </div>

    <div class="form-group">
        <?= Yii::t('app', 'Show') ?>:
        <?= empty($_GET['scenario']) ? 'all' : Html::a(Yii::t('app', 'all'), ['index']) ?>
        <?= isset($_GET['scenario']) && 'payment' == $_GET['scenario'] ? Yii::t('app', 'payments')
            : Html::a(Yii::t('app', 'payments'), ['index', 'scenario' => 'payment']) ?>
        <?= isset($_GET['scenario']) && 'withdraw' == $_GET['scenario'] ? Yii::t('app', 'withdrawals')
            : Html::a(Yii::t('app', 'withdrawals'), ['index', 'scenario' => 'withdraw']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>

</div>
