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
    return Html::tag('div', Html::submitButton($label, ['class' => 'btn btn-success']), ['class' => 'form-group']);
}

$form = ActiveForm::begin();

if ($model->isNewRecord) {
    echo $form->field($model, 'name');
}
echo $form->field($model, 'email');
if ('signup' == $model->scenario) {
    echo $form->field($model, 'password')->passwordInput();
    echo $form->field($model, 'repeat')->passwordInput();
}
echo $form->field($model, 'skype');
if (!$model->isNewRecord) {
    echo $form->field($model, 'duration')->textInput(['title' => Yii::t('app', 'Session duration')]);
    echo $form->field($model, 'country');

    $zones = timezone_identifiers_list();
    echo $form->field($model, 'timezone')->dropDownList(array_combine($zones, $zones));
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

echo $form->field($model, 'phone');
echo $form->field($model, 'forename');
echo $form->field($model, 'surname');

echo Html::tag('div', '* ' . Yii::t('app', 'Required fields'), ['class' => 'form-group']);

if (Yii::$app->user->isGuest) {
    echo submit(Yii::t('app', 'Signup'));
}
else {
    if ($model->isNewRecord) {
        echo submit(Yii::t('app', 'Create'));
    }
    else {
        echo submit(Yii::t('app', 'Update'));
    }
}

ActiveForm::end();
