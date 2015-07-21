<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\Record */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Journal');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="journal-index">

    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'type',
                'format' => 'html',
                'value' => function($record) {
                    return Yii::t('app', $record->type);
                }
            ],
            [
                'attribute' => 'event',
                'format' => 'html',
                'value' => function($record) {
                    return Html::a(Yii::t('app', $record->event), ['journal/view', 'id' => $record->id]);
                }
            ],
            [
                'attribute' => 'object_id',
                'format' => 'html',
                'value' => function($record) {
                    $object = $record->object;
                    if ($object) {
                        return Html::a($object, method_exists($object, 'url')
                            ? $object->url()
                            : [$record->type . '/view', 'id' => $object->id]);
                    }
                    else {
                        return Yii::t('app', 'Not exists');
                    }
                }
            ],
            [
                'attribute' => 'user_name',
                'format' => 'html',
                'value' => function($record) {
                    return Html::a($record->user_name, ['user/view', 'name' => $record->user_name]);
                }
            ],
            [
                'attribute' => 'data',
                'format' => 'html',
                'value' => function($record) {
                    $journal_view = Yii::getAlias('@app') . "/views/$record->type/journal.php";
                    if (file_exists($journal_view)) {
                        return Yii::$app->view->renderFile($journal_view, [
                            'record' => $record
                        ]);
                    }
                    else {
                        return $record->data ? json_encode($record->info) : '';
                    }
                }
            ],
            'time:datetime',
            'ip',
        ],
    ]); ?>

</div>
