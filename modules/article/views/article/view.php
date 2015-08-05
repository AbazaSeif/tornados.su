<?php
/**
 * @link http://zenothing.com/
 */

use app\widgets\Ext;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\article\models\Article */

$this->title = $model->title;
$this->registerMetaTag([
    'name' => 'description',
    'content' => substr(strip_tags($model->content), 0, 150) . '...'
]);

if (!$model->name) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'News'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}
?>
<article class="article-view" itemscope itemtype="//schema.org/Article">
    <?= Ext::stamp() ?>

    <h1 itemprop="name"><?= Html::encode($this->title) ?></h1>

    <p class="form-group">
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isManager()): ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary']); ?>

            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <div itemprop="articleBody">
        <?= $model->content ?>
    </div>

</article>
