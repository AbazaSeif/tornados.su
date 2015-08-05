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

$reset = !$model || 'reset' == $model->scenario;
$this->title = Yii::t('app', $reset ? 'Reset Password' : 'Set Password');
if (!$reset) {
    if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isManager()) {
        $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
    }
    $this->params['breadcrumbs'][] = ['label' => $model->user_name, 'url' => ['view', 'name' => $model->user_name]];
    $this->params['breadcrumbs'][] = Yii::t('app', 'Password');
}

if ($message):
    echo "<div class=\"alert alert-danger\">$message</div>";
else:
    ?>
    <div class="user-password middle">
        <?= Ext::stamp() ?>
        <h1><?= $this->title ?></h1>
        <?php $form = ActiveForm::begin(); ?>

        <?php if (!$reset) {
            echo $form->field($model, 'password')->passwordInput();
        }
        elseif (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
            echo Html::hiddenInput('name', $model->user_name);
        }
        ?>
        <?= $form->field($model, 'new_password')->passwordInput() ?>
        <?= $form->field($model, 'repeat_password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php
endif;
