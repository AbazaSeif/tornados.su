<?php
/**
 * @link http://zenothing.com/
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\feedback\models\Feedback */

$this->title = $model->subject;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Feedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
?>
<div class="feedback-view">

    <article>
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
        <p>
            <?php
            if ($model->email) {
                echo Html::tag('strong', $model->username);
                echo ' ' . Html::a($model->email, 'mailto:' . $model->email);
            }
            else {
                echo Html::a($model->username, ['user/view', 'name' => $model->username]);
            }
            ?>
            <?= Html::tag('i', $model->time) ?>
            <?= $model->ip ?>
        </p>
        <?= $model->content ?>
    </article>

</div>
