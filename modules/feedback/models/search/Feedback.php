<?php
/**
 * @link http://zenothing.com/
 */

namespace app\modules\feedback\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\feedback\models\Feedback as FeedbackModel;

/**
 * @author Taras Labiak <kissarat@gmail.com>
 * Feedback represents the model behind the search form about `app\models\Feedback`.
 */
class Feedback extends FeedbackModel
{
    public function rules() {
        return [
            [['id'], 'integer'],
            [['username', 'email', 'subject', 'content'], 'safe'],
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
        $query = FeedbackModel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
