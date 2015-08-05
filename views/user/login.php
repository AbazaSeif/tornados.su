<?php
/**
 * @link http://zenothing.com/
*/

use app\widgets\Ext;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Login */
/* @var $form ActiveForm */

$this->title = Yii::t('app', 'Login');
?>
<div class="user-login middle">
    <?= Ext::stamp() ?>
    <h1 class="bagatelle"><?= $this->title ?></h1>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    <?= $form->field($model, 'remember')->checkbox() ?>

    <div class="form-group">
        <?= Yii::t('app', 'You can recover <a href="{url}">your password</a>', [
            'url' => Url::to(['request']),
        ]); ?>
    </div>
    <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div><!-- user-login -->
