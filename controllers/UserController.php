<?php
/**
 * @link http://zenothing.com/
 */

namespace app\controllers;

use app\behaviors\Access;
use app\behaviors\Journal;
use app\models\Login;
use app\models\Password;
use app\models\Record;
use app\models\ResetRequest;
use app\helpers\SQL;
use app\modules\pyramid\models\Node;
use app\modules\pyramid\models\Type;
use Yii;
use app\models\User;
use app\models\search\User as UserSearch;
use yii\base\InvalidParamException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class UserController extends Controller
{
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

            'access' => [
                'class' => Access::class,
                'plain' => ['view', 'update'],
                'manager' => ['index', 'complete', 'account'],
                'admin' => ['delete']
            ]
        ];
    }

    public function actionIndex() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($name = null) {
        if (!$name && Yii::$app->user->isGuest) {
            throw new InvalidParamException();
        }

        $model = $name ? $this->findModel($name) : Yii::$app->user->identity;
        if (Yii::$app->user->isGuest
            || (!Yii::$app->user->identity->isManager() && Yii::$app->user->identity->name != $model->name)) {
            throw new ForbiddenHttpException();
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate() {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'name' => $model->name]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($name) {
        $model = $this->findModel($name);

        if ($model->name != Yii::$app->user->identity->name && !Yii::$app->user->identity->isAdmin()) {
            throw new ForbiddenHttpException('Forbidden');
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->identity->isAdmin()) {
                if ($model->isAttributeChanged('email', false)) {
                    $model->generateCode();
                    $url = Url::to(['email', 'code' => $model->code], true);
                    $model->sendEmail([
                        'subject' => Yii::$app->params['site']['name'] . ' ' . Yii::t('app', 'Email confirmation'),
                        'content' => Yii::t('app', 'To confirm your email click on the <a href="{url}">link</a>', [
                            'url' => $url,
                        ])
                    ]);
                    $model->setBundle(['email' => $model->email]);
                    $model->email = $model->getOldAttribute('email');
                    Yii::$app->session->setFlash('info', Yii::t('app', 'The new email address will be changed after you confirm it'));
                }
                if ($model->isAttributeChanged('perfect', false)) {
                    $model->perfect = $model->getOldAttribute('perfect');
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Wallet can change admin only'));
                }
            }

            if ('Europe/Moscow' == $model->timezone) {
                $model->timezone = null;
            }

            foreach(['skype', 'country', 'phone', 'forename', 'surname'] as $name) {
                if (empty($model->$name)) {
                    $model->$name = null;
                }
            }

            if (Yii::$app->user->identity->name == $model->name && $model->isAttributeChanged('timezone', false)) {
                $_SESSION['timezone'] = $model->timezone;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'name' => $model->name]);
            }
        }

        if (!$model->timezone) {
            $model->timezone = 'Europe/Moscow';
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($name) {
        $this->findModel($name)->delete();
        return $this->redirect(['index']);
    }

    /**
     * @param string $name
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($name) {
        if (($model = User::findOne(['name' => $name])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSignup() {
        $model = new User([
            'scenario' => 'signup'
        ]);

        $bundle = [];
        if (!empty($_GET)) {
            $attributes = $model->activeAttributes();
            foreach ($_GET as $key => $value) {
                if (in_array($key, $attributes)) {
                    $model->$key = $value;
                } else {
                    $bundle[$key] = $value;
                }
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $bundle = array_merge(Yii::$app->request->post('bundle'), $bundle);

            if (preg_match('|U\w{7}|', $model->name)) {
                $model->addError('name', Yii::t('app', Yii::t('app', 'Username cannot be in the format of Perfect Money wallet')));
            } elseif ($model->validate()) {
                $model->generateCode();
                $model->status = User::PLAIN;
                $model->setBundleFromAttributes(['hash'], true, $bundle);
                $model->save(false);
                $url = Url::to(['email', 'code' => $model->code], true);
                if ($model->sendEmail([
                    'subject' => Yii::$app->params['site']['name'] . ' ' . Yii::t('app', 'Signup'),
                    'content' => Yii::t('app', 'To confirm your registration click on the <a href="{url}">link</a>', [
                        'url' => $url,
                    ])
                ])
                ) {
                    Yii::$app->session->setFlash('info', Yii::t('app', 'Check your email'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Email send error'));
                }
                return $this->redirect(['home/index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'bundle' => $bundle
        ]);
    }

    public function actionLogin() {
        $model = new Login();

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->getUser();
            if ($user) {
                if (empty($user->hash)) {
                    Yii::$app->session->setFlash('error', Yii::t('app',
                        Yii::t('app', 'Your account is not activated. Check your email')));
                }
                else {
                    $can = $user->canLogin();
                    if ($can && $user->validatePassword($model->password)) {
                        if ($user->status > 0) {
                            if (empty($user->auth)) {
                                $user->generateAuthKey();
                                $user->save();
                            }
                            if (Yii::$app->user->login($user, $model->remember ? $user->duration * 60 : 0)) {
                                $bundle = $user->getBundle();
                                if ($bundle && isset($bundle['node_id'])) {
                                    $node_id = (int) $bundle['node_id'];
                                    $user->setBundle(null);
                                    $user->save();
                                    if (Node::find()->where(['id' => $node_id])->count() > 0) {
                                        Yii::$app->session->addFlash('success', Yii::t('app', 'Congratulation! You receive a gift'));
                                        return $this->redirect(['/pyramid/node/index', 'id' => $node_id]);
                                    }
                                }
                                return $this->redirect(['view']);

                            }
                            else {
                                Yii::$app->session->addFlash('error', Yii::t('app', 'Something wrong happened'));
                            }
                        } else {
                            Yii::$app->session->setFlash('error', Yii::t('app', Yii::t('app', 'Your account is blocked')));
                        }
                    } else {
                        Journal::write('user', 'login_fail', $user->id);
                        if ($can) {
                            Yii::$app->session->setFlash('error', Yii::t('app', 'Invalid username or password'));
                        } else {
                            $record = Record::find()->where([
                                'object_id' => $user->id,
                                'event' => 'login_fail'
                            ])->orderBy(['time' => SORT_DESC])->one();
                            Yii::$app->session->setFlash('error',
                                Yii::t('app', 'You have exceeded the maximum number of login attempts, you will be able to enter after {time}', [
                                    'time' => $record->time
                                ]));
                        }
                    }
                }
            }
            else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Invalid username or password'));
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return $this->redirect(['home/index']);
    }

    public function actionEmail($code) {
        /** @var User $user */
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()) {
            $user = User::findOne(['name' => $code]);
        }
        else {
            $user = preg_match('|^[\w\-_]{64}$|', $code) ? User::findOne(['code' => $code]) : null;
        }
        if ($user) {
            $bundle = $user->getBundle();
            $message = empty($user->hash)
                ? 'Congratulations. You have successfully activated!'
                : 'Your email changed!';
            $attributes = $user->activeAttributes();
            $attributes[] = 'hash';
            $redirect = ['user/view'];
            foreach($bundle as $name => $value) {
                if (in_array($name, $attributes)) {
                    $user->$name = $value;
                }
                elseif (isset($bundle['type_id'])) {
                    $type = Type::get($bundle['type_id']);
                    $redirect = ['/invoice/invoice/create', 'amount' => $type->stake];
                    $_SESSION['type_id'] = $type->id;
                }
            }
            $user->code = null;
            $user->setBundle(null);
            if ($user->save()) {
                Yii::$app->session->addFlash('success', Yii::t('app', $message));
                Yii::$app->user->login($user);
                return $this->redirect($redirect);
            }
            else {
                Yii::$app->session->addFlash('error', Yii::t('app', 'Something wrong happened'));
            }
        }
        else {
            Yii::$app->session->addFlash('error', Yii::t('app', 'Invalid code'));
        }
        return $this->redirect(['home/index']);
    }

    public function actionPassword($code = null, $name = null) {
        /** @var User $user */
        $message = null;
        $model = null;
        if (isset($_POST['name'])) {
            $name = $_POST['name'];
        }
        if ($name) {
            if (Yii::$app->user->identity->isAdmin() || $name == Yii::$app->user->identity->name) {
                $user = User::findOne(['name' => $name]);
            }
            else {
                throw new ForbiddenHttpException();
            }
        }
        else {
            $user = $code ? User::findOne(['code' => $code]) : Yii::$app->user->identity;
        }
        if ($user) {
            $model = new Password([
                'scenario' => ($code || $name) ? 'reset' : 'default',
                'user' => $user
            ]);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ('reset' == $model->scenario) {
                    $user->code = null;
                    if (!$user->auth) {
                        $user->generateAuthKey();
                    }
                    $user->setPassword($model->new_password);
                    if ($user->save()) {
                        Yii::$app->session->addFlash('success', Yii::t('app', 'Password saved'));
                        return Yii::$app->user->isGuest
                            ? $this->redirect(['user/login'])
                            : $this->redirect(['user/view', 'name' => $user->name]);
                    } else {
                        $message = json_encode($user->errors, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    if ($user->validatePassword($model->password)) {
                        $user->setPassword($model->new_password);
                        if ($user->save()) {
                            Yii::$app->session->addFlash('success', Yii::t('app', 'Password saved'));
                            return $this->redirect(['user/view', 'name' => $user->name]);
                        }  else {
                            $message = json_encode($user->errors, JSON_PRETTY_PRINT, JSON_UNESCAPED_UNICODE);
                        }
                    }
                    else {
                        $model->addError('password', Yii::t('app', 'Invalid password'));
                    }
                }
            }
        }
        else {
            $message = Yii::t('app', 'Invalid code');
        }
        return $this->render('password', [
            'model' => $model,
            'message' => $message
        ]);
    }

    public function actionRequest() {
        /** @var User $user */
        $model = new ResetRequest();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['email' => $model->email]);
            $user->generateCode();
            if ($user->save()) {
                $url = Url::to(['password', 'code' => $user->code], true);
                if($user->sendEmail([
                    'subject' => Yii::$app->params['site']['name'] . ' ' . Yii::t('app', 'Password reset'),
                    'content' => Yii::t('app', 'To recover your password, open <a href="{url}">this link</a>', [
                        'url' => $url,
                    ])
                ])) {
                    Yii::$app->session->setFlash('info', Yii::t('app', 'Check your email'));
                    return $this->redirect(['home/index']);
                }
                else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to send mail'));
                }
            }
            else {
                Yii::$app->session->setFlash('error', json_encode($user->errors));
            }
        }
        return $this->render('request', [
            'model' => $model
        ]);
    }

    public function actionAccount() {
        return $this->render('account', [
            'model' => SQL::queryObject('SELECT * FROM "account"')
        ]);
    }

    public function actionComplete($search) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return User::find()
            ->select('name')
            ->where('name like :name', [
                ':name' => "$search%"
            ])
            ->limit(10)
            ->column();
    }
}
