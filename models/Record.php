<?php
/**
 * @link http://zenothing.com/
*/

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "journal".
 *
 * @property integer $id
 * @property string $type
 * @property string $event
 * @property integer $object_id
 * @property resource $data
 * @property string $user_name
 * @property string $time
 * @property integer $ip
 * @author Taras Labiak <kissarat@gmail.com>
 */
class Record extends ActiveRecord
{
    private $_object;
    private static $_classes = [
        'user' => 'app\models\User',
        'invoice' => 'app\modules\invoice\models\Invoice',
        'feedback' => 'app\modules\feedback\models\Feedback',
    ];

    private static $_cache = [];

    public function getObject() {
        if ($this->object_id && !$this->_object) {
            if (isset(static::$_cache[$this->object_id])) {
                $this->_object = static::$_cache[$this->object_id];
            }
            else {
                $this->_object = forward_static_call([static::$_classes[$this->type], 'findOne'], [$this->object_id]);
                static::$_cache[$this->object_id] = $this->_object;
            }
        }
        return $this->_object;
    }

    public function getInfo() {
        return unserialize($this->data);
    }

    public static function tableName() {
        return 'journal';
    }

    public function rules() {
        return [
            [['type', 'event'], 'required'],
            [['object_id', 'ip'], 'integer'],
            [['data'], 'string'],
            [['time'], 'safe'],
            [['type', 'event'], 'string', 'max' => 16],
            [['user_name'], 'string', 'max' => 24]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'event' => Yii::t('app', 'Event'),
            'object_id' => Yii::t('app', 'Object ID'),
            'data' => Yii::t('app', 'Data'),
            'user_name' => Yii::t('app', 'User Name'),
            'time' => Yii::t('app', 'Time'),
            'ip' => Yii::t('app', 'IP'),
        ];
    }
}
