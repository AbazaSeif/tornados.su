<?php
/**
 * @link http://zenothing.com/
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\feedback\models\Feedback */

$this->title = Yii::t('app', 'Create Feedback');
?>
<div class="feedback-create middle">

    <h1 class="bagatelle"><?= Html::encode(Yii::t('app', 'Create Feedback')) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
