<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Offer;

/**
 * OfferSearch represents the model behind the search form of `app\models\Offer`.
 */
class OfferSearch extends Offer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'own_category_id', 'available', 'price', 'created_at', 'updated_at'], 'integer'],
            [['url', 'pictures', 'currency', 'name', 'description', 'params'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = Offer::find()->with(['ownCategory', 'categories']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'own_category_id' => $this->own_category_id,
            'available' => $this->available,
            'price' => $this->price,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);
        return $dataProvider;
    }
}
