<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\models\TestCase;

/**
 * TestCaseSearch represents the model behind the search form about `app\models\TestCase`.
 */
class TestCaseSearch extends TestCase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tcid', 'priority', 'serverity', 'repoid'], 'integer'],
            [['tctitle', 'area', 'category', 'tag', 'CreateDate'], 'safe'],
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
        $query = TestCase::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [ 'pageSize' => 10 ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tcid' => $this->tcid,
            'priority' => $this->priority,
            'serverity' => $this->serverity,
            'repoid' => $this->repoid,
            'CreateDate' => $this->CreateDate,
        ]);

        $query->andFilterWhere(['like', 'tctitle', $this->tctitle])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'tag', $this->tag]);

        return $dataProvider;
    }

    /**
     * 
     */
    public function searchRelevantTestCase($params)
    {
        // 当前用户参与的项目，且用户在项目中的 role 为 'M'
        $cond = ['in', 'repoid', (new Query())->select('repoid')->from('JoinRepo')->where(['uid' => Yii::$app->user->id,'role' => 'M'])];
        $query = TestCase::find()->where($cond);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
        ]);
/*
        $dataProvider->sort->attributes['repo.reponame'] = [
            'asc' => ['repo.reponame'  => SORT_ASC],
            'desc' => ['repo.reponame' => SORT_DESC],
        ];
*/

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tcid' => $this->tcid,
            'priority' => $this->priority,
            'serverity' => $this->serverity,
            'repoid' => $this->repoid,
            'CreateDate' => $this->CreateDate,
        ]);

        $query->andFilterWhere(['like', 'tctitle', $this->tctitle])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'tag', $this->tag]);

        return $dataProvider;
    }
}
