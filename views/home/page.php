<?php
/**
 * @link http://zenothing.com/
*/
use yii\bootstrap\Html;

$title = $model->getTitle();
if ($title) {
    $this->title = $title;
}
?>
<div class="page">
    <?php
    if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isManager()) {
        echo Html::a(Yii::t('app', 'Update'), ['update', 'name' => $model->name], ['class' => 'btn btn-primary']);
    }
    ?>

    <article><?= $model->content ?></article>
</div>
