<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\article\models;

use app\behaviors\Journal;
use Yii;
use yii\db\ActiveRecord;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $content
 */
class Article extends ActiveRecord
{
    public function behaviors() {
        return [
            Journal::class
        ];
    }

    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string', 'min' => 20],
            [['title'], 'string', 'min' => 3, 'max' => 255]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
        ];
    }

    public function __toString() {
        return $this->title;
    }

    public function url() {
        return ['/article/article/view', 'id' => $this->id];
    }
}
