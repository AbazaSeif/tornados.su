<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\models;

use app\models\User;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * This is the model class for table "income".
 *
 * @property integer $id
 * @property integer $node_id
 * @property string $user_name
 * @property integer $type_id
 * @property integer $time
 *
 * @property Type $type
 * @property Node $node
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
        return $this->hasOne(Node::class, ['id' => 'node_id']);
    }

    public static function makeFromNode(Node $node) {
        if ($node->type->isTornado()) {
            $node = Node::getTornadoExpectant();
        }
        return static::make($node->id, $node->user, $node->type_id, $node->time, $node->getType()->income);
    }

    public static function make($node_id, User $user, $type_id, $time, $amount) {
        $user->account += (float) $amount;
        if ($user->update(false, ['account'])) {
            $income = new static([
                'node_id' => $node_id,
                'user_name' => $user->name,
                'type_id' => $type_id,
                'time' => $time
            ]);
            if ($income->save()) {
                return $income;
            }
            else {
                throw new Exception(json_encode($income->getErrors(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
        else {
            throw new Exception(json_encode(func_get_args()));
        }
    }
}
