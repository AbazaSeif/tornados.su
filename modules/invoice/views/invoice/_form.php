<?php
/**
 * @link http://zenothing.com/
*/

use app\modules\invoice\models\Invoice;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\invoice\models\Invoice */
/* @var $form yii\widgets\ActiveForm */
$statuses = [];
foreach(Invoice::$statuses as $key => $value) {
    $statuses[$key] = Yii::t('app', $value);
}
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin();

    if ('manage' == $model->scenario) {
        echo $form->field($model, 'user_name')->textInput(['maxlength' => true]);
        echo $form->field($model, 'status')->dropDownList($statuses);
    }
    echo$form->field($model, 'amount')->textInput(['maxlength' => true]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
