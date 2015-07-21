<?php
/**
 * @link http://zenothing.com/
*/

/**
 * @var app\models\Record $record
 */

use app\modules\invoice\models\Invoice;

$info = $record->info;
if ($info) {
    if (isset($info['status'])) {
        echo Yii::t('app', Invoice::$statuses[$info['status']]);
    }
}
