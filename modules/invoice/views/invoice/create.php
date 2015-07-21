<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\invoice\models\Invoice */

$this->title = Yii::t('app', 'payment' == $model->scenario ? 'Payment' : 'Withdraw');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Invoices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
