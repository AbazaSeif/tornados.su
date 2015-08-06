<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\pyramid\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\pyramid\models\Income as IncomeModel;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Income represents the model behind the search form about `app\models\Archive`.
 */
class Income extends IncomeModel
{
    public function rules() {
        return [
            [['id', 'node_id', 'type_id', 'time'], 'integer'],
            [['user_name'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Income::find()
            ->with('node');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['time' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'node_id' => $this->node_id,
            'type_id' => $this->type_id,
            'time' => $this->time
        ]);

        $query->andFilterWhere(['like', 'user_name', isset($params['user']) ? $params['user'] : $this->user_name]);

        return $dataProvider;
    }
}
