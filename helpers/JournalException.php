<?php
/**
 * @link http://zenothing.com/
*/

namespace app\helpers;


use app\behaviors\Journal;
use Exception;
use Yii;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class JournalException extends Exception {
    public $type;
    public $object_id;
    public $event;

    public function __construct($type, $object_id, $event, $message = null, $previous = null) {
        parent::__construct($message, static::getHttpCode($event), $previous);
        $this->type = $type;
        $this->object_id = $object_id;
        $this->event = $event;
        Journal::write($type, $event, $object_id, $message);
    }

    public static function getHttpCode($message) {
        static $messages;
        if (!$messages) {
            $messages = [];
            $lines = file_get_contents(__DIR__ . '/http_code.txt');
            $lines = explode("\n", $lines);
            foreach($lines as $line) {
                if ($line) {
                    $line = explode("\t", $line);
                    $messages[$line[1]] = (int) $line[0];
                }
            }
        }
        return $messages[$message];
    }
}
