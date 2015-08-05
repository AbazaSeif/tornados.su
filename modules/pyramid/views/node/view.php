<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Node */

$this->title = $model->type->name . " #$model->id";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Queue'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Investment'), 'url' => ['invest']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-view middle">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>

    <div id="matrix">
        <h2><?= count($model->children) ?> из <?= $model->type->degree ?></h2>
        <?php
        foreach($model->children as $child) {
            echo Html::a($child->user_name, ['user/view', 'name' => $child->user_name]);
        }
        ?>
    </div>
</div>
