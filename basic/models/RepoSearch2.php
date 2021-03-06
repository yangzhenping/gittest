<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Repo;

/**
 * RepoSearch represents the model behind the search form about `app\models\Repo`.
 */
class RepoSearch2 extends Repo
{
    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['user.uname']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['repoid', 'adminid'], 'integer'],
            [['reponame','RegisterDate','user.uname'], 'safe'],
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
        $query = Repo::find()->where(['ishide'=>'n']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith('user as user');
        $dataProvider->sort->attributes['user.uname'] = [
            'asc' => ['user.uname' => SORT_ASC],
            'desc' => ['user.uname' => SORT_DESC],
            
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'repoid' => $this->repoid,
            'RegisterDate' => $this->RegisterDate,
        ]);

        $query->andFilterWhere(['LIKE', 'user.uname', $this->getAttribute('user.uname')]);

        $query->andFilterWhere(['like', 'reponame', $this->reponame]);

        return $dataProvider;
    }
}
