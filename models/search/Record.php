<?php
/**
 * @link http://zenothing.com/
 */

namespace app\models\search;

use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\Record as RecordModel;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Journal represents the model behind the search form about `app\models\Journal`.
 */
class Record extends RecordModel
{
    public function rules() {
        return [
            [['id', 'object_id', 'ip'], 'integer'],
            [['type', 'event', 'data', 'user_name', 'time'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = RecordModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['time' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'object_id' => $this->object_id,
            'time' => $this->time,
            'ip' => $this->ip,
        ]);

        if (isset($params['user'])) {
            $user = User::findOne(['name' => $params['user']]);
            $query->andWhere(['or',
                ['user_name' => $user->name],
                ['object_id' => $user->id]
            ]);
        }

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'event', $this->event])
            ->andFilterWhere(['like', 'data', $this->data])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
