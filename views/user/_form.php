<?php
/**
 * @link http://zenothing.com/
 */

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

function submit($label) {
    echo '<div class="form-group">';
    echo Html::submitButton($label, ['class' => 'btn btn-success']);
    echo '</div>';
}

$form = ActiveForm::begin();

if ($model->isNewRecord) {
    echo $form->field($model, 'name');
}
echo $form->field($model, 'email');
if ('signup' == $model->scenario) {
    echo $form->field($model, 'password')->passwordInput();
}
echo $form->field($model, 'skype');
if (!$model->isNewRecord) {
    echo $form->field($model, 'duration');
}
if ($model->isNewRecord || Yii::$app->user->identity->isAdmin()) {
    echo $form->field($model, 'perfect');
}
else {
    $a = Html::a('обратитесь к адмнинистратору', ['feedback/create', 'template' => 'wallet']);
    echo "<div class='form-group'>Для изменения кошелька $a</div>";
}
if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
    echo $form->field($model, 'account');
    echo $form->field($model, 'status')->dropDownList(User::statuses());
}

if (Yii::$app->user->isGuest) {
    submit(Yii::t('app', 'Signup'));
}
else {
    if ($model->isNewRecord) {
        submit(Yii::t('app', 'Create'));
    }
    else {
        submit(Yii::t('app', 'Update'));
    }
}

ActiveForm::end();

echo '</div>';
