<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\models;

use app\helpers\Account;
use app\models\User;
use Exception;
use Yii;
use yii\db\ActiveRecord;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * This is the model class for table "node".
 *
 * @property integer $id
 * @property string $user_name
 * @property integer $type_id
 * @property integer $count
 * @property integer $time
 *
 * @property Type $type
 * @property User $user
 * @property Node[] $children
 */
class Node extends ActiveRecord
{
    private $_children;

    public static function tableName() {
        return 'node';
    }

    public function rules() {
        return [
            [['user_name', 'type_id', 'count', 'time'], 'required'],
            [['type_id', 'count', 'time'], 'integer', 'min' => 0],
            [['user_name'], 'string', 'max' => 24],
            ['time', 'default', 'value' => $_SERVER['REQUEST_TIME']]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_name' => Yii::t('app', 'Username'),
            'type_id' => Yii::t('app', 'Plan'),
            'count' => Yii::t('app', 'Counter'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['name' => 'user_name']);
    }

    public function setUser(User $value) {
        $this->user_name = $value->name;
    }

    /**
     * @return Type
     */
    public function getType() {
        return $this->hasOne(Type::className(), ['id' => 'type_id']);
    }

    public function setType(Type $value) {
        $this->type_id = $value->id;
    }

    public function getChildren() {
        if (!$this->_children) {
            $this->_children = Node::find()->where(['>', 'id', $this->id])->limit(40)->all();
        }
        return $this->_children;
    }

    public function countQueue() {
        return static::find()
            ->andWhere('time <= :time', [':time' => $this->time])
            ->andWhere(['type_id' => $this->type_id])
            ->sum('count');
    }

    public function decrement() {
        $this->count--;
        return $this->update(false, ['count']);
    }

    public function invest() {
        $this->count = $this->type->degree;
        $this->time = $_SERVER['REQUEST_TIME'];
        if ($expectant = $this->getExpectant($this->type_id)) {
            if ($expectant->count > 1) {
                $expectant->decrement();
            } else {
                $user = $expectant->user;
                $expectant->delete();

                Yii::$app->db->createCommand(
                    'INSERT INTO "income"(node_id,  user_name,  type_id, "time")
                          VALUES (:node_id, :user_name, :type_id, :time)', [
                    ':node_id' => $expectant->id,
                    ':user_name' => $expectant->user_name,
                    ':type_id' => $expectant->type_id,
                    ':time' => $expectant->time
                ])
                    ->execute();

                $user->account += (float)$this->type->income;
                if (!$user->update(false, ['account'])) {
                    throw new Exception($this->dump());
                }
            }
        }
        if ($this->save()) {
            Account::set('profit', $this->type->profit);
            return true;
        }
        return false;
    }

    /**
     * @param $type_id
     * @return Node
     */
    public static function getExpectant($type_id) {
        return static::find()->where(['type_id' => $type_id])->andWhere('count > 0')
            ->orderBy(['time' => SORT_ASC, 'id' => SORT_ASC])->limit(1)->one();
    }

    public function dump() {
        $bundle = [
            'id' => $this->id,
            'type_id' => $this->type_id,
            'count' => $this->count,
            'time' => date($this->time),
            'user_name' => $this->user_name,
            'account' => $this->user->account
        ];
        if (count($this->errors) > 0) {
            $bundle['errors'] = $this->errors;
        }
        if (count($this->user->errors) > 0) {
            $bundle['user_errors'] = $this->user->errors;
        }
        return json_encode($bundle, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function __toString() {
        return $this->id . ' ' . Type::get($this->type_id);
    }
}
