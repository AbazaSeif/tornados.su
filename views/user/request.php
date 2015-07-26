<?php
/**
 * @link http://zenothing.com/
*/

use app\widgets\Ext;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $message string
 * @var $model app\models\Password
 * @var $form ActiveForm
 */

$this->title = Yii::t('app', 'Request for password recovery');

?>
<div class="user-password">
    <?= Ext::stamp() ?>
    <h1><?= $this->title ?></h1>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
