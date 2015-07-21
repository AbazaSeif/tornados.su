<?php
/**
 * @link http://zenothing.com/
 */

namespace app\helpers;

use app\models\Record;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Class EventTrait
 * @property $record Record
 * @property $ip Record
 * @property $time Record
 */
trait JournalTrait {
    public function getRecord() {
        return $this->hasOne(Record::className(), ['object_id' => 'id']);
    }

    public function getTime() {
        return $this->record->time;
    }

    public function getIp() {
        return long2ip($this->record->ip);
    }
}
