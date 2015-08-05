<?php
/**
 * @link http://zenothing.com/
 */

use app\modules\pyramid\models\Gift;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('app', 'Gifts');
?>
<div class="gift-index middle">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{pager}\n{errors}\n{summary}\n{items}",
        'columns' => [
            'id',
            [
                'attribute' => 'user_name',
                'format' => 'html',
                'value' => function(Gift $model) {
                    return Html::a($model->user_name, ['user/view', 'name' => $model->user_name]);
                }
            ],
            'time:datetime',
            [
                'attribute' => 'node_id',
                'label' => Yii::t('app', 'Action'),
                'format' => 'html',
                'value' => function(Gift $model) {
                    if ($model->node_id) {
                        return Html::a(Yii::t('app', 'View'),
                            ['index', 'id' => $model->node_id],
                            ['class' => 'btn btn-primary btn-xs']);
                    }
                    else {
                        return Html::a(Yii::t('app', 'Give'),
                            ['give', 'id' => $model->id],
                            ['class' => 'btn btn-success btn-sm']);
                    }
                }
            ],
        ],
    ]); ?>

</div>
