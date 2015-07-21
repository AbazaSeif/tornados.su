<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\invoice\components;


use yii\base\Component;
use yii\base\Exception;
use yii\base\Model;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Class PerfectMoney
 * @package app\components
 * @property int id
 * @property string password
 * @property string wallet
 * @property string merchant
 * @property string alternateSecret
 */
class PerfectMoney extends Component {
    public $id;
    public $password;
    public $wallet;
    public $merchant;
    public $alternateSecret;

    public function hashAlternateSecret() {
        return strtoupper(md5($this->alternateSecret));
    }

    public function queryHistory($end = null, $start = null) {
        if (!$end)
            $end = time();
        if (!$start)
            $start = $end - 3600 * 24;
        $start = explode('-', gmdate('Y-m-d', $start));
        $end = explode('-', gmdate('Y-m-d', $end));
        $params = [
            'AccountID' => $this->id,
            'PassPhrase' => $this->password,
            'startyear' => $start[0],
            'startmonth' => $start[1],
            'startday' => $start[2],
            'endyear' => $end[0],
            'endmonth' => $end[1],
            'endday' => $end[2],
            'paymentsreceived' => 1
        ];
        $params = http_build_query($params);
        $url = 'https://perfectmoney.is/acct/historycsv.asp?' . $params;
        $response = file_get_contents($url);
        //file_put_contents('/tmp/history', "$url\n\n$response");
        $rows = explode("\n", $response);
        $table = [];
        foreach($rows as $row) {
            $table[] = explode(',', $row);
        }
        $headers = array_flip($table[0]);
        $table = array_slice($table, 1);

        if (count($headers) < 5) {
            throw new Exception(count($headers));
        }
        $table = array_slice($table, 1);
        $fields = Payment::labels();
        $payments = [];
        foreach($table as $row) {
            if (empty($row[0]))
                continue;
            $payment = new Payment();
            foreach ($fields as $name => $label)
                $payment->$name = $row[$headers[$label]];
            $payments[] = $payment;
        }
        return $payments;
    }

    public function findPayment($id, $end = null, $start = null) {
        $payments = $this->queryHistory($end, $start);
        foreach($payments as $payment) {
            if ($id === $payment->id) {
                return $payment;
            }
        }
        return null;
    }
}

class Payment extends Model {
    public $id;
    public $batch;
    private $_time;
    public $amount;
    public $fee;
    public $sender;
    public $receiver;
    public $memo;

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'batch' => 'Транзакция',
            'time' => 'Время',
            'amount' => 'Количество',
            'fee' => 'Комиссия',
            'sender' => 'Отправитель',
            'receiver' => 'Получатель',
            'memo' => 'Заметка',
        ];
    }

    public static function labels() {
        return [
            'id' => 'Payment ID',
            'batch' => 'Batch',
            'time' => 'Time',
            'amount' => 'Amount',
            'fee' => 'Fee',
//            'sender' => 'Payer Account',
//            'receiver' => 'Payee Account',
            'sender' => 'Payee Account',
            'receiver' => 'Payer Account',
            'memo' => 'Memo',
        ];
    }

    public static function fromArray(array $array) {
        $record = new static();
        foreach($array as $key => $value)
            $record->$key = $value;
        return $record;
    }

    public function setTime($value) {
        if (is_string($value))
            $value = strtotime($value);
        $this->_time = $value;
    }

    public function getTime() {
        return $this->_time;
    }
}
