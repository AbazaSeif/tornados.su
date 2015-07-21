<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\invoice\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\invoice\models\Invoice as InvoiceModel;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Invoice represents the model behind the search form about `app\modules\invoice\models\Invoice`.
 */
class Invoice extends InvoiceModel
{
    public function rules() {
        return [
            [['id'], 'integer'],
            [['user_name', 'status'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = InvoiceModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
        ]);

        if (isset($params['user'])) {
            $query->andFilterWhere(['user_name' => $params['user']]);
        }

        if (isset($params['scenario'])) {
            $query->andWhere('withdraw' == $params['scenario'] ? 'amount < 0' : 'amount > 0');
        }

        $query->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
