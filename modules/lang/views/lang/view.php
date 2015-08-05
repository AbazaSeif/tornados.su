<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\lang\models\Translation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$vars = preg_match_all('|\{(\w+)\}|', $model->message, $matches, PREG_SET_ORDER);
//throw new Exception(json_encode($matches));
if ($vars) {
    $vars = [', ['];
    foreach($matches as $match) {
        $match = $match[1];
        $vars[] = "'$match' => '',";
    }
    $vars[] = ']';
    $vars = implode("\n", $vars);
}
else {
    $vars = '';
}

?>
<div class="translation-view middle">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Translation'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'message:ntext',
            'translation:ntext',
        ],
    ]) ?>

    <pre>Yii::t('app', '<?= Html::encode($model->message) ?>'<?= $vars ?>);</pre>

</div>
