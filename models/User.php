<?php
/**
 * @link http://zenothing.com/
 */

namespace app\models;


use app\behaviors\Journal;
use app\modules\pyramid\models\Income;
use app\modules\pyramid\models\Node;
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
 * @property string repeat
 * @property integer status
 * @property number account
 * @property string ref_name
 * @property integer duration
 * @property resource data
 * @property array bundle
 *
 * @property User referral
 */
class User extends ActiveRecord implements IdentityInterface {

    const BLOCKED = 0;
    const ADMIN = 1;
    const PLAIN = 2;
    const MANAGER = 3;
    const TEAM = 4;

    private $_password;
    private $_info;
    public $repeat;
    public $accept;

    public static function primaryKey() {
        return ['id'];
    }

    public static function statuses() {
        return [
            User::PLAIN => Yii::t('app', 'Registered'),
            User::BLOCKED => Yii::t('app', 'Blocked'),
            User::ADMIN => Yii::t('app', 'Admin'),
            User::MANAGER => Yii::t('app', 'Manager'),
            User::TEAM => Yii::t('app', 'Team')
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
            [['name', 'email', 'account', 'status'], 'required'],
            [['perfect', 'skype', 'account', 'phone', 'forename', 'surname'], 'required',
                'on' => ['signup', 'default']],
            [['password', 'repeat'], 'required', 'on' => 'signup'],
            ['name', 'string', 'min' => 4, 'max' => 24],
            ['name', 'match', 'pattern' => '/^[a-z][a-z0-9_\-]+$/i', 'on' => 'signup'],
            [['name'], 'unique',
                'targetClass' => 'app\models\User',
                'message' => Yii::t('app', 'This value has already been taken')],
            ['email', 'email'],
            ['perfect', 'match', 'pattern' => '/^U\d{7}$/', 'message' =>
                Yii::t('app', 'The wallet should looks like U1234567')],
            ['skype', 'match', 'pattern' => '/^[a-zA-Z][a-zA-Z0-9\.,\-_]{5,31}$/'],
            [['skype', 'timezone', 'country', 'phone', 'forename', 'surname', 'name', 'email', 'perfect'],
                'filter', 'filter' => 'trim'],
            [['skype', 'timezone', 'country', 'phone', 'forename', 'surname', 'name', 'email', 'perfect'],
                'default', 'value' => null],
            ['account', 'default', 'value' => 0],
            ['duration', 'integer', 'min' => 0, 'max' => 60 * 24 * 7],
            ['timezone', 'in', 'range' => timezone_identifiers_list()],
            ['repeat', 'compare', 'compareAttribute' => 'password'],
            [['forename', 'surname'], 'string', 'min' => 2, 'max' => 24],
            ['phone', 'match', 'pattern' => '/^\d{9,16}$/'],
            ['accept', 'compare', 'compareValue' => true]
        ];
    }

    public function scenarios() {
        return [
            'default' => ['email', 'skype', 'duration', 'country', 'timezone', 'phone', 'forename', 'surname'],
            'signup'  => ['name', 'email', 'skype', 'perfect', 'password', 'repeat', 'ref_name', 'phone', 'forename', 'surname', 'accept'],
            'admin'   => ['name', 'email', 'skype', 'perfect', 'account', 'status',
                'duration', 'country', 'timezone', 'phone', 'forename', 'surname', 'ref_name'],
        ];
    }

    public function attributeLabels() {
        return [
            'name' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'repeat' => Yii::t('app', 'Repeat'),
            'status' => Yii::t('app', 'Status'),
            'account' => Yii::t('app', 'Account'),
            'skype' => Yii::t('app', 'Skype'),
            'perfect' => Yii::t('app', 'Perfect Money wallet'),
            'duration' => Yii::t('app', 'Session duration (in minutes)'),
            'timezone' => Yii::t('app', 'Timezone'),
            'country' => Yii::t('app', 'Country'),
            'phone' => Yii::t('app', 'Phone'),
            'forename' => Yii::t('app', 'Forename'),
            'surname' => Yii::t('app', 'Surname'),
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

    public function isTeam() {
        return static::TEAM == $this->status || static::ADMIN == $this->status || static::MANAGER == $this->status;
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
            ->setFrom(['laskasevera@gmail.com' => Yii::$app->name])
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
        if (!$this->_info && $this->data) {
            $this->_info = unserialize(stream_get_contents($this->data));
        }
        return $this->_info;
    }

    public function setBundle($value) {
        $value = empty($value) ? null : serialize($value);
        $this->data = $value;
        $this->_info = $value;
    }

    public function setBundleFromAttributes($names, $restore = false, $bundle = []) {
        foreach($names as $name) {
            $bundle[$name] = $this->$name;
            if ($restore) {
                $this->$name = $this->getOldAttribute($name);
            }
        }
        $this->setBundle($bundle);
    }

    public function journalView() {
        return __DIR__ . '/../views/user/journal.php';
    }

    public function getReferral() {
        return $this->hasOne(static::class, ['name' => 'ref_name']);
    }

    public function getNodes() {
        return $this->hasMany(Node::class, ['user_name' => 'name']);
    }

    public function getSponsors() {
        return $this->hasMany(static::class, ['ref_name' => 'name']);
    }

    public function canChargeBonus() {
        //@todo decouple
        return $this->ref_name && (
            Node::find()->where(['user_name' => $this->ref_name])->count() > 0 ||
            Income::find()->where(['user_name' => $this->ref_name])->count() > 0);
    }
}
