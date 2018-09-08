<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UserLog;

/**
 * UserLogSearch represents the model behind the search form about `backend\models\UserLog`.
 */
class UserLogSearch extends UserLog
{
    /**
     * @inheritdoc
     */
    public $start_time;
    public $end_time;
    public $name;
    public $username;
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'add_time'], 'integer'],
            [['log_title', 'log_text'], 'safe'],
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
        $query = UserLog::find()
        ->from(['setting' => self::tableName()])
        ->select([
            'setting.*',
            'user.name',
            'user.username',
        ])
        ->leftJoin(['user' => User::tableName()], 'user.id = setting.user_id');
        ;

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
            'user_id' => $this->user_id,
            'type' => $this->type,
            'add_time' => $this->add_time,
        ]);

        $query->andFilterWhere(['like', 'log_title', $this->log_title])
            ->andFilterWhere(['like', 'log_text', $this->log_text]);

        return $dataProvider;
    }
}
