<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \app\modules\pyramid\models\Type */

$this->title = $model->getName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Marketing'), 'url' => ['plan']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Open'), ['open', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => ['method' => 'post']
            ]); ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'stake',
            'income'
        ],
    ]) ?>

</div>
