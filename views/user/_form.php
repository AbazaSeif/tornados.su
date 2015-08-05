<?php
/**
 * @link http://zenothing.com/
 */

use app\models\User;
use app\widgets\Ext;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $bundle array */
/* @var $form yii\widgets\ActiveForm */

function submit($label) {
    return Html::submitButton($label, ['class' => 'btn btn-success']);
}

echo Ext::stamp();

$form = ActiveForm::begin();

if ($model->isNewRecord) {
    foreach($bundle as $key => $value) {
        echo Html::hiddenInput("bundle[$key]", $value);
    }

    if ($model->ref_name) {
        echo Html::tag('div', Yii::t('app', 'Referral') . ': ' . $model->ref_name, ['class' => 'form-group']);
    }

    echo $form->field($model, 'name');
}

echo $form->field($model, 'email');
echo $form->field($model, 'skype');

if ('signup' == $model->scenario || 'admin' == $model->scenario) {
    echo $form->field($model, 'password')->passwordInput();
    echo $form->field($model, 'repeat')->passwordInput();
}

if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
    echo $form->field($model, 'account');
    echo $form->field($model, 'status')->dropDownList(User::statuses());
}

    echo $form->field($model, 'phone');
    echo $form->field($model, 'forename');
    echo $form->field($model, 'surname');

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
    echo Html::tag('div', Yii::t('app', 'To change a wallet you need <a href="{url}">write to admin</a>', [
        'url' => Url::to(['feedback/feedback/create', 'template' => 'wallet'])
    ]),
        ['class' => 'form-group']);
}

echo Html::tag('div', '* ' . Yii::t('app', 'Required fields'), ['class' => 'form-group']);

if ($model->isNewRecord && 'signup' == $model->scenario) {
    echo Html::tag('div',
    Html::activeCheckbox($model, 'accept', ['label' => Yii::t('app', 'I accept the <a href="{url}">terms and conditions</a>', [
            'url' => Url::to(['/article/article/page', 'name' => 'offer'])
        ])]),
    ['class' => 'offer']);
}
else {
    echo '<br />';
}

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
