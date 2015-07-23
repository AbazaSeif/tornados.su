<?php
/**
 * @link http://zenothing.com/
*/

namespace app\models;

use app\modules\invoice\models\Invoice;
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

    public function getObject() {
        if (is_null($this->_object)) {
            $this->_object = call_user_func([$this, $this->type]);
        }
        return $this->_object;
    }

    public function getInfo() {
        return unserialize($this->data);
    }

    public function user() {
        return User::findOne($this->object_id);
    }

    public function invoice() {
        return Invoice::findOne($this->object_id);
    }

    public static function tableName()
    {
        return 'journal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'event'], 'required'],
            [['object_id', 'ip'], 'integer'],
            [['data'], 'string'],
            [['time'], 'safe'],
            [['type', 'event'], 'string', 'max' => 16],
            [['user_name'], 'string', 'max' => 24]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
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
