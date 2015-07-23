<?php
/**
 * @link http://zenothing.com/
*/

namespace app\models;


use app\behaviors\Journal;
use Exception;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\User as WebUser;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Class User
 * @property integer id
 * @property string name
 * @property string auth
 * @property string email
 * @property string code
 * @property string hash
 * @property string wallet
 * @property string perfect
 * @property string timezone
 * @property string country
 * @property integer status
 * @property number account
 * @property integer duration
 * @property resource data
 * @property array bundle
 * @package app\models
 */
class User extends ActiveRecord implements IdentityInterface {

    const BLOCKED = 0;
    const ADMIN = 1;
    const PLAIN = 2;
    const MANAGER = 3;

    private $_password;
    private $_info;

    public static function primaryKey() {
        return ['id'];
    }

    public static function statuses() {
        return [
            User::BLOCKED => Yii::t('app', 'Blocked'),
            User::ADMIN => Yii::t('app', 'Admin'),
            User::PLAIN => Yii::t('app', 'Registered'),
            User::MANAGER => Yii::t('app', 'Manager')
        ];
    }

    public static $events = [
        'login' => 'Вход',
        'logout' => 'Выход',
        'login_fail' => 'Неудачная попытка входа',
    ];

    public function traceable() {
        return ['status', 'email', 'skype',  'perfect', 'account'];
    }

    public function url() {
        return ['user/view', 'name' => $this->name];
    }

    public function rules() {
        return [
            ['id', 'integer'],
            [['name', 'email', 'perfect', 'account'], 'required'],
            ['password', 'required', 'on' => 'signup'],
            ['name', 'string', 'min' => 4, 'max' => 24],
            ['name', 'match', 'pattern' => '/^[a-z][a-z0-9_\-]+$/i', 'on' => 'signup'],
            ['email', 'email'],
            ['perfect', 'match', 'pattern' => '/^U\d{7}$/', 'message' =>
                Yii::t('app', 'The wallet should looks like U1234567')],
            ['skype', 'match', 'pattern' => '/^[a-zA-Z][a-zA-Z0-9\.,\-_]{5,31}$/'],
            [['skype', 'timezone', 'country'], 'default', 'value' => null],
            [['name'], 'filter', 'filter' => 'trim'],
            [['name'], 'unique',
                'targetClass' => 'app\models\User',
                'message' => Yii::t('app', 'This value has already been taken')],
            ['duration', 'integer', 'min' => 0, 'max' => 60 * 24 * 7],
            ['timezone', 'in', 'range' => timezone_identifiers_list()]
        ];
    }

    public function scenarios() {
        return [
            'default' => ['email', 'skype', 'duration', 'country', 'timezone'],
            'signup'  => ['name', 'email', 'skype', 'perfect', 'password'],
            'admin'   => ['name', 'email', 'skype', 'perfect', 'account', 'status', 'duration', 'country', 'timezone'],
        ];
    }

    public function attributeLabels() {
        return [
            'name' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'account' => Yii::t('app', 'Account'),
            'skype' => Yii::t('app', 'Skype'),
            'perfect' => Yii::t('app', 'Perfect Money wallet'),
            'duration' => Yii::t('app', 'Session duration (in minutes)'),
        ];
    }

    public function behaviors() {
        return [
            Journal::class
        ];
    }

    public function init() {
        parent::init();
        if (isset(Yii::$app->user)
            && Yii::$app->user instanceof WebUser
            && !Yii::$app->user->getIsGuest()
            && static::ADMIN == Yii::$app->user->identity->status) {
            $this->scenario = 'admin';
        }
    }

    /**
     * @param string $id
     * @return User
     */
    public static function findIdentity($id) {
        return parent::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        throw new Exception('Not implemented findIdentityByAccessToken');
    }

    public function getId() {
        return $this->id;
    }

    public function getAuthKey() {
        return $this->auth;
    }

    public function validateAuthKey($authKey) {
        return $authKey == $this->auth;
    }

    public function validatePassword($password) {
        return password_verify($password, $this->hash);
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setPassword($value) {
        $this->hash = password_hash($value, PASSWORD_DEFAULT);
        $this->_password = $value;
    }

    public function generateAuthKey() {
        $this->auth = Yii::$app->security->generateRandomString(64);
    }

    public function generateCode() {
        $this->code = Yii::$app->security->generateRandomString(64);
    }

    public function isManager() {
        return static::ADMIN == $this->status || static::MANAGER == $this->status;
    }

    public function isAdmin() {
        return static::ADMIN == $this->status;
    }

    public function __toString() {
        return $this->name;
    }

    public function sendEmail($params)
    {
        $template = Yii::getAlias('@app') . "/views/mail.php";
        return Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setFrom(['diamond4rush@gmail.com' => Yii::$app->name])
            ->setSubject($params['subject'])
            ->setHtmlBody(Yii::$app->view->renderFile($template, $params))
            ->send();
    }

    public function canLogin() {
        return Record::find()->andWhere([
            'object_id' => $this->id,
            'event' => 'login_fail'
        ])
            ->andWhere('(NOW() - "time") < interval \'5 minutes\'')
            ->count() < 30;
    }

    public function getBundle() {
        if (!$this->_info) {
            $this->_info = unserialize(stream_get_contents($this->data));
        }
        return $this->_info;
    }

    public function setBundle($value) {
        $value = empty($value) ? null : serialize($value);
        $this->data = $value;
        $this->_info = $value;
    }

    public function setBundleFromAttributes($names, $restore = false) {
        $bundle = [];
        foreach($names as $name) {
            $bundle[$name] = $this->$name;
            if ($restore) {
                $this->$name = $this->getOldAttribute($name);
            }
        }
        $this->setBundle($bundle);
    }

    public function getNodes() {
        return $this->hasMany(Node::class, ['user_name' => 'name']);
    }
}
