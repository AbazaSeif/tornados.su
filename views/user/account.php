<?php
/**
 * @link http://zenothing.com/
 */
use yii\helpers\Html;

?>
<div class="account">
    <h1><?= Yii::t('app', 'Accounts') ?></h1>
    <table class="table">
        <?php
        foreach($model as $key => $value) {
            echo Html::tag('tr',
                Html::tag('td', Yii::t('app', $key)) .
                Html::tag('td', $value)
            );
        }
        ?>
    </table>
</div>
