<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\IosVersion;

/**
 * IosVersionSearch represents the model behind the search form about `backend\models\IosVersion`.
 */
class IosVersionSearch extends IosVersion
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['iosDownload', 'iosForce', 'iosUpdateMsg', 'iosVersion', 'type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = IosVersion::find();

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
        ]);

        $query->andFilterWhere(['like', 'iosDownload', $this->iosDownload])
            ->andFilterWhere(['like', 'iosForce', $this->iosForce])
            ->andFilterWhere(['like', 'iosUpdateMsg', $this->iosUpdateMsg])
            ->andFilterWhere(['like', 'iosVersion', $this->iosVersion])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
