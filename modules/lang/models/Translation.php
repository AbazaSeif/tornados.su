<?php
/**
 * @link http://zenothing.com/
*/

namespace app\modules\lang\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "translation".
 *
 * @property integer $id
 * @property string $message
 * @property string $translation
 * @author Taras Labiak <kissarat@gmail.com>
 */
class Translation extends ActiveRecord
{
    public function rules() {
        return [
            [['id'], 'integer'],
            [['message', 'translation'], 'string']
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'message' => Yii::t('app', 'Message'),
            'translation' => Yii::t('app', 'Translation'),
        ];
    }
}
