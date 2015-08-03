<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\controllers;


use app\behaviors\Access;
use app\modules\pyramid\models\Gift;
use app\modules\pyramid\models\Income;
use app\modules\pyramid\models\Node;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use app\modules\pyramid\models\Type;
use yii\web\NotFoundHttpException;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 */
class TypeController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'open' => ['post'],
                ]
            ],

            'access' => [
                'class' => Access::class,
                'plain' => ['view']
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index', [
            'dataProvider' => new ArrayDataProvider([
                'allModels' => Type::all(),
//                'sort' => [
//                    'defaultOrder' => ['id' => SORT_ASC]
//                ]
            ]),
        ]);
    }

    public function actionView($id) {
        $model = $this->findModel($id);
        if (Yii::$app->user->identity->account < $model->stake && !Yii::$app->session->getFlash('success')) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Insufficient funds'));
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionOpen($id) {
        if (!in_array($id, [1, 2, 3, 4])) {
            throw new InvalidParamException('id');
        }

        /** @var \app\models\User $me */
        $me = Yii::$app->user->identity;
        $type = $this->findModel($id);
        $node = new Node([
            'type' => $type,
            'user' => $me
        ]);

        if ($me->account >= $type->stake) {
            $me->account -= $type->stake;
            $transaction = Yii::$app->db->beginTransaction();
//            try {
                $sum = (int) Node::find()->where(['user_name' => $me->name])->count();
                $sum += (int) Income::find()->where(['user_name' => $me->name])->count();
                if ($me->update(true, ['account']) && $node->invest()) {
                    if (0 == $sum && $me->canChargeBonus()) {
                        $referral = $me->referral;
                        $referral->account += $type->bonus;
                        $referral->update(true, ['account']);
                        if (4 == $type->id) {
                            $count = $referral->getSponsors()
                                ->select('user_name')
                                ->joinWith('nodes')
                                ->groupBy('user_name')->count();
                            if ($count > 0 && 0 == $count % 10) {
                                $gift = new Gift(['user_name' => $referral->name]);
                                $gift->save();
                                Yii::$app->session->setFlash('success', Yii::t('app', 'Your referral may receive a gift'));
                            }
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'The plan is open'));
                }
//            }
//            catch(\Exception $ex) {
//                $transaction->rollBack();
//                Yii::$app->session->setFlash('error', $ex->getMessage());
//            }
        }
        else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Insufficient funds'));
            return $this->redirect(['view', 'id' => $node->id]);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if ($model = Type::get($id)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
