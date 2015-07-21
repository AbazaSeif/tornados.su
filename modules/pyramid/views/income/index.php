<?php
/**
 * @link http://zenothing.com/
 */

use app\modules\pyramid\models\Type;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View
 * @var $searchModel \app\modules\pyramid\models\search\Income
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('app', 'Income');
?>
<div class="archive-index">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{pager}\n{errors}\n{summary}\n{items}",
        'columns' => [
            'id',
            [
                'attribute' => 'node_id',
                'label' => Yii::t('app', 'Investment'),
                'format' => 'html',
                'value' => function($model) {
                    $node = $model->node;
                    return is_null($node) ? $model->node_id : Html::a($node, ['node', 'id' => $model->node_id]);
                }
            ],
            [
                'attribute' => 'user_name',
                'format' => 'html',
                'value' => function($model) {
                    return Html::a($model->user_name, ['user/view', 'name' => $model->user_name]);
                }
            ],
            [
                'attribute' => 'type_id',
                'label' => Yii::t('app', 'Plan'),
                'format' => 'html',
                'value' => function($model) {
                    return Html::a($model->type, ['view', 'id' => $model->type_id]);
                }
            ],
            [
                'attribute' => 'type_id',
                'label' => Yii::t('app', 'Income'),
                'value' => function($model) {
                    return Type::get($model->type_id)->income;
                }
            ],
            'time:datetime',
        ],
    ]); ?>

</div>
