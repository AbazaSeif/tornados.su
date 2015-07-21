<?php
/**
 * @link http://zenothing.com/
*/

use yii\helpers\Html;

/* @var $exception \app\JournalException */

$this->title = $exception->event;
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
</div>
