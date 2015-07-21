<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\lang\models\Translation */

$this->title = Yii::t('app', 'Create Translation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="translation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
