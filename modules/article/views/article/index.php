<?php
/**
 * @link http://zenothing.com/
 */

use app\widgets\Ext;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\article\models\Article */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
?>
<div class="article-index middle">
    <?= Ext::stamp() ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php /*echo $this->render('_search', ['model' => $searchModel]);*/
    if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isManager()) {
        echo Html::tag('p', Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']));
    }
    ?>

    <ul class="list-group">
        <?php
        echo ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => function($model) {
                echo Html::tag('li', Html::a($model->title, ['view', 'id' => $model->id]),
                    ['class' => 'list-group-item']);
            }
        ]);
        ?>
    </ul>

</div>
