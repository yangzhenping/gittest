<?php

namespace app\controllers;

use Yii;
use app\models\Repo;
use app\models\RepoSearch;
use app\models\RepoSearch2;
use app\models\RepoSearch3;
use app\models\JoinRepo;
use app\models\RequestSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * RepoController implements the CRUD actions for Repo model.
 */
class RepoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','create','update','delete'],
                'rules' => [
                    [
                        'actions' => ['index','create','update','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Repo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RepoSearch(['adminid'=>Yii::$app->user->id]);
	    $searchModel2 = new RepoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider2 = $searchModel2->searchMyJoinedProject(Yii::$app->request->queryParams);

        $requestDataProvider = (new RequestSearch())->search();

        return $this->render('index', [
            'searchModel' => $searchModel,
	    'searchModel2' => $searchModel2,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'requestDataProvider' => $requestDataProvider,
        ]);
    }

    /**
    * 列出指定用户创建的所有公共 repo
    */
    public function actionIndexForUser($uid)
    {
        // 查询的用户即为当前的用户
        if (Yii::$app->user->id == $uid) {
            return $this->redirect(['repo/index']);
        }

        $searchModel = new RepoSearch3(['adminid'=>$uid]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index_for_user', [
            'user' => User::findOne($uid),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionIndexall()
    {
        $searchModel = new RepoSearch2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexall', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
    /**
     * Displays a single Repo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->adminid == Yii::$app->user->id || strtolower($model->ishide) == 'n') {
            $testManagers = $model->testManagers;
            $testers = $model->testers;
            return $this->render('view', [
                'model' => $model,
                'testManagers' => $testManagers,
                'testers' => $testers
            ]);                      
        } else {
            throw new \yii\web\ForbiddenHttpException;
        }


    }

    /**
     * Creates a new Repo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Repo([
            'adminid' => Yii::$app->user->id,
            'ishide' => 'n',
            ]);
        $request = Yii::$app->request;

        if($request->isPost) { 
            $post = $request->post();
            // 确保ishide字段存储的为小写
            $post['Repo']['ishide'] = strtolower($post['Repo']['ishide']);
        }

        if ($request->isPost && $model->load($post) && $model->save()) {

            $joinRepo = new JoinRepo();
            $joinRepo->uid = Yii::$app->user->id;
            $joinRepo->repoid = $model->repoid;
            $joinRepo->IsApproved = 'y';
            $joinRepo->role = 'M';
            $joinRepo->save();

            return $this->redirect(['view', 'id' => $model->repoid]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Repo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (\Yii::$app->user->can('changeRepo',['entity'=>$model])) {         
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->repoid]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            throw new \yii\web\ForbiddenHttpException;
        }
    }

    /**
     * Deletes an existing Repo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (\Yii::$app->user->can('changeRepo',['entity'=>$model])) {
            $model->delete();
            return $this->redirect(['index']);           
        } else {
            throw new \yii\web\ForbiddenHttpException;
        }

    }

    /**
     * Finds the Repo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Repo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Repo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
