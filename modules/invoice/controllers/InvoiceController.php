<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\invoice\controllers;

use app\behaviors\Access;
use app\behaviors\NoTokenValidation;
use app\modules\invoice\models\Withdrawal;
use app\modules\invoice\models\Invoice;
use app\modules\invoice\models\search\Invoice as InvoiceSearch;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class InvoiceController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'success' => ['post'],
                    'delete' => ['post'],
                ],
            ],

            'access' => [
                'class' => Access::className(),
                'plain' => ['success', 'fail', 'index', 'view', 'create'],
                'manager' => ['withdraw', 'update', 'delete']
            ],

            'no_csrf' => [
                'class' => NoTokenValidation::className(),
                'only' => ['success', 'fail'],
            ]
        ];
    }

    public function actionIndex($user = null, $scenario = null) {
        $searchModel = new InvoiceSearch();
        if (!Yii::$app->user->identity->isManager()) {
            if (!$user) {
                $url = ['index', 'user' => Yii::$app->user->identity->name];
                if ($scenario) {
                    $url['scenario'] = $scenario;
                }
                return $this->redirect($url);
            }
            elseif (!Yii::$app->user->identity->isManager() && $user != Yii::$app->user->identity->name) {
                throw new ForbiddenHttpException('Forbidden');
            }
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($scenario = 'payment') {
        $model = new Invoice([
            'user_name' => Yii::$app->user->identity->name,
            'scenario' => $scenario
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if ('withdraw' == $model->scenario) {
                $model->amount = - abs($model->amount);
                if (abs($model->amount) > $model->user->account) {
                    Yii::$app->session->setFlash('error', Yii::t('app', Invoice::$statuses['insufficient_funds']));
                    $model->amount = $model->user->account;
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'manage';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id) {
        $invoice = $this->findModel($id);
        if ('success' == $invoice->status) {
            $invoice->saveStatus('delete');
        }
        else {
            $invoice->delete();
        }

        return $this->redirect(['index']);
    }

    public function actionSuccess($id) {
        $transaction = Yii::$app->db->beginTransaction();
        $invoice = $this->findModel($id);
        $string = $string= $_POST['PAYMENT_ID']
            .':'.$_POST['PAYEE_ACCOUNT']
            .':'.$_POST['PAYMENT_AMOUNT']
            .':'.$_POST['PAYMENT_UNITS']
            .':'.$_POST['PAYMENT_BATCH_NUM']
            .':'.$_POST['PAYER_ACCOUNT']
            .':'. Yii::$app->perfect->hashAlternateSecret()
            .':'.$_POST['TIMESTAMPGMT'];
        $user = $invoice->user;
        if (Yii::$app->user->identity->name != $invoice->user_name) {
            $transaction->rollBack();
            throw new ForbiddenHttpException(Yii::t('app', 'You can only change the status of your payments'));
        }
        elseif ('success' == $invoice->status) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Payment has already done previously'));
        }
        elseif (strtoupper(md5($string)) != $_POST['V2_HASH']) {
            $invoice->saveStatus('invalid_hash');
            Yii::$app->session->setFlash('error', Yii::t('app', Invoice::$statuses['invalid_hash']));
        }
        elseif ($invoice->amount != $_POST['PAYMENT_AMOUNT']) {
            $invoice->saveStatus('invalid_amount');
            Yii::$app->session->setFlash('error', Yii::t('app', Invoice::$statuses['invalid_amount']));
        }
        elseif (Yii::$app->perfect->wallet != $_POST['PAYEE_ACCOUNT']) {
            $invoice->saveStatus('invalid_receiver');
            Yii::$app->session->setFlash('error', Yii::t('app', 'Wrong recipient {wallet}', [
                'wallet' =>  $_POST['PAYEE_ACCOUNT'],
            ]));
        }
        elseif ('USD' != $_POST['PAYMENT_UNITS']) {
            $invoice->saveStatus('invalid_currency');
            Yii::$app->session->setFlash('error', Yii::t('app', Invoice::$statuses['invalid_currency']));
        }
        else {
            $user->account += $invoice->amount;
            if ($invoice->saveStatus('success') && $user->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', Yii::t('app', 'Payment #{id} completed', [
                    'id' => $invoice->id,
                ])));
            }
            else {
                $invoice->saveStatus('fail');
                Yii::$app->session->setFlash('error', Yii::t('app', 'Cannot save payment'));
            }
        }
        $transaction->commit();

        return $this->render('view', [
            'model' => $invoice
        ]);
    }

    public function actionFail($id) {
        $invoice = $this->findModel($id);

        if (Yii::$app->user->identity->name != $invoice->user_name) {
            throw new ForbiddenHttpException(Yii::t('app', 'You can only change the status of your payments'));
        }
        else {
            $invoice->saveStatus('cancel');
            Yii::$app->session->setFlash('error', Yii::t('app', 'You cancel payment'));
        }

        return $this->render('view', [
            'model' => $invoice
        ]);
    }


    public function actionWithdraw($id) {
        if (!Yii::$app->perfect->id) {
            $invoice = $this->findModel($id);
            $invoice->saveStatus('success');
            return $this->render('view', [
                'model' => $invoice
            ]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $invoice = $this->findModel($id);
        $invoice->scenario = 'withdraw';
        try {
            if ('success' == $invoice->status) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('info', Yii::t('app', 'Payment has already done previously'));
            }
            elseif (abs($invoice->amount) > $invoice->user->account) {
                $invoice->throwJournalException(Yii::t('app', Invoice::$statuses['insufficient_funds']));
            }
            else {
                $withdrawal = Withdrawal::fromInvoice($invoice);
                $response = file_get_contents('https://perfectmoney.is/acct/confirm.asp?' . $withdrawal);
                if (!preg_match('/<h1>(.*)<\/h1>/', $response, $result)) {
                    $invoice->throwJournalException($response);
                }
                elseif ('Spend' != $result[1]) {
                    $invoice->throwJournalException($result[1]);
                }
                elseif (!preg_match_all("/<input name='(.*)' type='hidden' value='(.*)'>/",
                    $response, $result, PREG_SET_ORDER)) {
                    $invoice->throwJournalException($response);
                }
                else {
                    $info = [];
                    foreach ($result as $row) {
                        $info[$row[1]] = $row[2];
                    }
                    if (isset($info["ERROR"])) {
                        $invoice->throwJournalException($info["ERROR"]);
                    }
                    elseif ($info['PAYMENT_AMOUNT'] != abs($invoice->amount)) {
                        $invoice->throwJournalException(Yii::t('app', 'Invalid amount') . ' ' . $info['PAYMENT_AMOUNT']);
                    }
                    else {
                        $invoice->batch = $info['PAYMENT_BATCH_NUM'];
                        $invoice->status = 'success';
                        $invoice->user->account -= abs($invoice->amount);
                        if ($invoice->user->save() && $invoice->save()) {
                            $transaction->commit();
                            Yii::$app->session->setFlash('success', Yii::t('app', 'Payment #{id} completed', [
                                'id' => $invoice->id,
                            ]));
                        }
                        else {
                            Yii::$app->session->setFlash('error', json_encode(
                                array_merge($invoice->user->errors, $invoice->errors),
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        }
                    }
                }
            }
        }
        catch(Exception $ex) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
        return $this->render('view', [
            'model' => $invoice
        ]);
    }


    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            $model->scenario = $model->amount < 0 ? 'withdraw' : 'payment';
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
