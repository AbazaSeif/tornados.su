<?php
/**
 * @link http://zenothing.com/
*/

use app\models\Type;
use app\widgets\AjaxComplete;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Node */
/* @var $form yii\widgets\ActiveForm */

$types = Type::getItems();
?>

<div class="type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_name')->widget(AjaxComplete::class, [
        'route' => ['user/complete']
    ]) ?>
    <?= $form->field($model, 'type_id')->dropDownList($types) ?>
    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'count');
    }
    ?>
    <?php
    array_unshift($types, Yii::t('app', 'Investment'), 0);
    unset($types[1]);
    echo $form->field($model, 'reinvest_from')->dropDownList($types);
    echo Html::activeHiddenInput($model, 'time');
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord
            ? Yii::t('app', 'Create')
            : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
