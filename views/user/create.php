<?php
/**
 * @link http://zenothing.com/
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $bundle array */

$this->title = Yii::$app->user->isGuest ? Yii::t('app', 'Signup') : Yii::t('app', 'Create User');
?>
<div class="user-create">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bundle' => $bundle
    ]) ?>

</div>
