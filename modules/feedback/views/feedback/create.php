<?php
/**
 * @link http://zenothing.com/
 */

use app\widgets\Ext;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\feedback\models\Feedback */

$this->title = Yii::t('app', 'Create Feedback');

if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isManager()) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Feedbacks'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = Yii::t('app', 'Create');
}
?>
<div class="feedback-create middle">
    <?= Ext::stamp() ?>

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
