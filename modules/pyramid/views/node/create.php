<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Type */

$this->title = Yii::t('app', 'Create Investment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Queue'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
