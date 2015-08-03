<?php
/**
 * @link http://zenothing.com/
 */

use app\widgets\Ext;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $models \app\modules\faq\models\Faq [] */

$this->title = Yii::t('app', 'FAQ');
$manager = !Yii::$app->user->isGuest && Yii::$app->user->identity->isManager();
?>
<div class="faq-index">
    <?= Ext::stamp() ?>
    <h1 class="bagatelle"><?= Html::encode($this->title) ?></h1>
    <br/>

    <?php
    if ($manager): ?>
        <p class="form-group">
            <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif ?>

    <div class="contents">
        <?php foreach($models as $model): ?>
            <a href="#<?= $model->id ?>"><?= $model->question ?></a>
        <?php endforeach ?>
    </div>

    <dl>
        <?php foreach($models as $model): ?>
            <dt id="<?= $model->id ?>">
                <?= $model->question ?>
                <?php
                if ($manager) {
                    echo Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) . ' ';
                    echo Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                }
                ?>
            </dt>
            <dd><?= $model->answer ?></dd>
        <?php endforeach ?>
    </dl>

</div>
