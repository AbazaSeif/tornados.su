<?php
/**
 * @link http://zenothing.com/
*/
use yii\helpers\Html;
?>
<div class="statistics">
<?php
foreach($statistics as $name => $value) {
    echo Html::tag('div', Html::tag('span', Yii::t('app', $name) . ':') . Html::tag('span', $value));
}
?>
</div>
