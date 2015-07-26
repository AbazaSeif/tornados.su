<?php
/**
 * @link http://zenothing.com/
*/

namespace app\modules\feedback\models;

use app\behaviors\Journal;
use app\helpers\JournalTrait;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "feedback".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $subject
 * @property string $content
 * @property \app\models\Record $record
 * @author Taras Labiak <kissarat@gmail.com>
 */
class Feedback extends ActiveRecord
{
    use JournalTrait;

    public $verifyCode;

    public function behaviors() {
        return [
            Journal::className(),
        ];
    }

    public static function tableName() {
        return 'feedback';
    }

    public function rules() {
        return [
            [['username', 'email'], 'required'],
            ['username', 'string', 'max' => 24],
            ['email', 'email'],
            [['subject', 'content'], 'required'],
            [['subject', 'content'], 'string'],
        ];
    }

    public function scenarios() {
        return [
            'default' => ['subject', 'content'],
            'guest' => ['username', 'email', 'subject', 'content'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Name'),
            'email' => Yii::t('app', 'Email'),
            'subject' => Yii::t('app', 'Subject'),
            'content' => Yii::t('app', 'Content'),
        ];
    }

    public function __toString() {
        return $this->subject;
    }
}
