<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\faq\models;


use yii\db\ActiveRecord;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Class Faq
 * @package app\modules\faq\models
 * @property int $id
 * @property string $question
 * @property string $answer
 */
class Faq extends ActiveRecord {

    public static function tableName() {
        return 'faq';
    }

    public function rules() {
        return [
            [['question', 'answer'], 'required']
        ];
    }

    public function attributeLabels() {
        return [
            'question' => 'Вопрос',
            'answer' => 'Ответ'
        ];
    }
}