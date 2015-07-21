<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\invoice\models;


use Yii;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Class Withdrawal
 * @package frontend\models
 * @property int AccountID
 * @property string PassPhrase
 * @property string Payer_Account
 * @property string Payee_Account
 * @property number Amount
 * @property int PAY_IN
 * @property int PAYMENT_ID
 */

class Withdrawal {
    public $AccountID;
    public $PassPhrase;
    public $Payer_Account;
    public $Payee_Account;
    public $Amount;
    public $PAY_IN = 1;
    public $PAYMENT_ID;

    public static function fromInvoice(Invoice $invoice) {
        $withdrawal = new Withdrawal();
        $withdrawal->AccountID = Yii::$app->perfect->id;
        $withdrawal->PassPhrase = Yii::$app->perfect->password;
        $withdrawal->PAYMENT_ID = $invoice->id;
        $withdrawal->Payer_Account = Yii::$app->perfect->wallet;
        $withdrawal->Payee_Account = $invoice->user->perfect;
        $withdrawal->Amount = abs($invoice->amount);
        return $withdrawal;
    }

    public function __toString() {
        $params = [];
        foreach($this as $key => $value)
            $params[$key] = $value;
        return http_build_query($params);
    }
}
