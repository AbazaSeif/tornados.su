<?php
/**
 * @link http://zenothing.com/
 */

namespace app\behaviors;


use Exception;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class Journal extends Behavior {
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'writeEvent',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'writeEvent',
            ActiveRecord::EVENT_BEFORE_DELETE => 'writeEvent',
        ];
    }

    public static function writeEvent(Event $event) {
        switch($event->name) {
            case ActiveRecord::EVENT_AFTER_INSERT:
                $name = 'create';
                break;
            case ActiveRecord::EVENT_BEFORE_UPDATE:
                $name = 'update';
                break;
            case ActiveRecord::EVENT_BEFORE_DELETE:
                $name = 'delete';
                break;
            default:
                $name = $event->name;
                break;
        }

        $model = $event->sender;
        $data = $event->data;
        if (('create' == $name || 'update' == $name) && method_exists($model, 'traceable')) {
            if (is_null($data)) {
                $data = [];
            }
            elseif (!is_array($data)) {
                throw new InvalidParamException(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
            foreach($model->traceable() as $attribute) {
                if ('create' == $name && $model->$attribute || $model->isAttributeChanged($attribute, false)) {
                    $data[$attribute] = $model->$attribute;
                }
            }
        }

        $table = $event->sender->tableName();
        if (0 === strpos($table, '{{%')) {
            $table = substr($table, 3, -2);
        }
        static::write($table, $name, $model->id, $data);
    }

    public static function writeException($type, $event, $object_id = null, Exception $exception) {
        static::write($type, $event, $object_id, $exception);
    }

    public static function write($type, $event, $object_id = null, $data = null) {
        if (empty($data)) {
            $data = null;
        }
        else {
            $data = serialize($data);
        }

        $user = Yii::$app->user;
        Yii::$app->db->createCommand('INSERT INTO journal(type, event, object_id, data, user_name, ip)
            VALUES (:type, :event, :object_id, :data, :user_name, :ip)', [
            ':type' => $type,
            ':event' => $event,
            ':object_id' => $object_id,
            ':data' => $data,
            ':user_name' => $user->getIsGuest() ? null : $user->identity->name,
            ':ip' => $_SERVER['REMOTE_ADDR']
        ])->execute();
    }
}
