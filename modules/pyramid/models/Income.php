<?php
/**
 * @link http://zenothing.com/
*/

namespace app\modules\pyramid\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "income".
 *
 * @property integer $id
 * @property integer $node_id
 * @property string $user_name
 * @property integer $type_id
 * @property integer $time
 * @author Taras Labiak <kissarat@gmail.com>
 */
class Income extends ActiveRecord
{
    public static function tableName() {
        return 'income';
    }

    public function rules() {
        return [
            [['node_id', 'user_name', 'type_id', 'time'], 'required'],
            [['node_id', 'type_id', 'time'], 'integer'],
            [['user_name'], 'string', 'max' => 24]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'node_id' => Yii::t('app', 'Investment ID'),
            'user_name' => Yii::t('app', 'Username'),
            'type_id' => Yii::t('app', 'Plan'),
            'reinvest_from' => Yii::t('app', 'Reinvest From'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @return Type
     */
    public function getType() {
        return Type::get($this->type_id);
    }

    /**
     * @return ActiveQuery
     */
    public function getNode() {
        return $this->hasOne(Node::className(), ['id' => 'node_id']);
    }
}
