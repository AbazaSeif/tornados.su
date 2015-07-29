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
class Gift extends ActiveRecord
{
    public static function tableName() {
        return 'gift';
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'node_id' => Yii::t('app', 'Investment ID'),
            'user_name' => Yii::t('app', 'Username'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    public function give() {
        $transaction = Yii::$app->db->beginTransaction();
        $node = new Node([
            'type_id' => 4,
            'user_name' => $this->user_name
        ]);

        if ($node->invest()) {
            $this->node_id = $node->id;
            $this->save();
            $node->user->setBundle([
                'node_id' => $node->type_id
            ]);
            $node->user->save();
            $transaction->commit();
            return $node;
        }

        $transaction->rollBack();
        return false;
    }
}
