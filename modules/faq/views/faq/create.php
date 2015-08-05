<?php
/**
 * @link http://zenothing.com/
 */

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model \app\modules\faq\models\Faq */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'FAQ'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faq-create middle">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
