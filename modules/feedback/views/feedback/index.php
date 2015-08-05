<?php
/**
 * @link http://zenothing.com/
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\feedback\models\search\Feedback */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Feedbacks');
?>
<div class="feedback-index contain">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Feedback'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'contentOptions' => ['class' => 'id']
            ],
            [
                'attribute' => 'username',
                'format' => 'html',
                'value' => function($model) {
                    if ($model->email) {
                        return $model->username;
                    }
                    else {
                        return Html::a($model->username, ['user/view', 'name' => $model->username]);
                    }
                }
            ],
            [
                'attribute' => 'email',
                'format' => 'html',
                'value' => function($model) {
                    if ($model->email) {
                        return Html::a($model->email, 'mailto:' . $model->email);
                    }
                    else {
                        return Yii::t('app', 'registered');
                    }
                }
            ],

            'subject:ntext',

            [
                'attribute' => 'content',
                'format' => 'html',
                'value' => function($model) {
                    return substr($model->content, 0, 40);
                }
            ],

            'time:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'action']
            ],
        ],
    ]); ?>

</div>
