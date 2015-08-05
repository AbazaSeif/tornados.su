<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\invoice\models;

use app\behaviors\Journal;
use app\helpers\JournalException;
use app\models\User;
use Yii;
use yii\db\ActiveRecord;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * This is the model class for table "invoice".
 *
 * @property integer $id
 * @property string $user_name
 * @property number $amount
 * @property integer $batch
 * @property string $status
 *
 * @property User $user
 */
class Invoice extends ActiveRecord
{
    public static $statuses = [
        'create' => 'Created',
        'invalid_amount' => 'Invalid amount',
        'invalid_receiver' => 'Invalid receiver',
        'invalid_batch' => 'Transaction ID does not match',
        'invalid_response' => 'Unknown server response',
        'invalid_hash' => 'Invalid hash',
        'invalid_currency' => 'Invalid currency',
        'insufficient_funds' => 'Insufficient funds in the account user',
        'no_qualification' => 'User does not qualify',
        'cancel' => 'Cancel',
        'fail' => 'Error',
        'success' => 'Completed'
    ];

    public static function tableName() {
        return 'invoice';
    }

    public function traceable() {
        return ['status'];
    }

    public function behaviors() {
        return [
            Journal::class
        ];
    }

    public function rules() {
        return [
            [['user_name', 'amount'], 'required'],
            [['amount'], 'number'],
            [['user_name', 'status'], 'string', 'max' => 16]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function find() {
        return parent::find()->andWhere('"status" <> \'delete\'');
    }

    public function scenarios() {
        return [
            'default' => ['user_name', 'amount', 'status'],
            'payment' => ['user_name', 'amount'],
            'withdraw' => ['user_name', 'amount'],
            'manage' => ['user_name', 'amount', 'status'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_name' => Yii::t('app', 'Username'),
            'amount' => Yii::t('app', 'Amount'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getUser() {
        return $this->hasOne(User::class, ['name' => 'user_name']);
    }

    public function saveStatus($status) {
        $this->status = $status;
        return $this->save();
    }

    public function __toString() {
        return Yii::t('app', $this->amount < 0 ? 'Withdraw #{id} for ${amount}' : 'Payment #{id} for ${amount}', [
            'id' => $this->id,
            'amount' => abs($this->amount)
        ]);
    }

    public function throwJournalException($message) {
        throw new JournalException(static::tableName(), $this->id, 'fail', $message);
    }

    public function journalView() {
        return __DIR__ . '/../views/invoice/journal.php';
    }

    public function url() {
        return ['/invoice/invoice/view', 'id' => $this->id];
    }
}
