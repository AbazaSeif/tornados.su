<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Record */

$event = Yii::t('app', $model->event);
$this->title = "$model->type $event";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Journal'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="journal-view middle">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            [
                'attribute' => 'event',
                'format' => 'html',
                'value' => $event
            ],
            'object_id',
            'user_name',
            'time',
            'ip',
        ]
    ]) ?>

    <?php
    if ($model->data) {
        $data = unserialize($model->data);
        echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
    }

    ?>

</div>
