<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * This is the model class for table "type".
 *
 * @property integer $id
 * @property number $stake
 * @property number $income
 * @property number $profit
 * @property integer $degree
 *
 * @property Type $next
 */
class Type extends ActiveRecord
{
    private static $_all;
    public $degree = 2;

    public static function tableName() {
        return 'type';
    }

    public function rules() {
        return [
            [['name'], 'string'],
            [['stake', 'income'], 'required'],
            [['stake', 'income'], 'number', 'min' => 0],
            [['next_id', 'degree'], 'integer', 'min' => 0]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'stake' => Yii::t('app', 'Stake'),
            'income' => Yii::t('app', 'Income'),
        ];
    }

    /**
     * @return Type
     */
    public function getNext() {
        $types = static::all();
        return isset($types[$this->next_id]) ? $types[$this->next_id] : null;
    }

    /**
     * @return Type[]
     */
    public static function all() {
        /* @var $type Type */
        if (!static::$_all) {
            $types = static::find()->orderBy(['id' => SORT_ASC])->all();
            static::$_all = [];
            foreach($types as $type) {
                static::$_all[$type->id] = $type;
            }
        }
        return static::$_all;
    }

    /**
     * @param $id
     * @return Type|null
     */
    public static function get($id) {
        $types = Type::all();
        return isset($types[$id]) ? $types[$id] : null;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @return array
     */
    public static function getItems() {
        $items = [];
        foreach(static::all() as $type) {
            $items[$type->id] = Yii::t('app', $type->name);
        }
        return $items;
    }

    public function getName() {
        return Yii::t('app', 'Plan') . ' ' . $this->id;
    }

    /**
     * @return integer
     */
    public function getProfit() {
        return $this->stake * 2 - $this->income;
    }
}
