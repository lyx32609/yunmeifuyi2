<?php

namespace backend\controllers;

use Yii;
use backend\models\UserLog;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\UserLogSearch;
use components\helpers\DateHelper;
use backend\models\User;
use backend\models\LoginForm;

/**
 * UserLogController implements the CRUD actions for UserLog model.
 */
class UserLogController extends Controller
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
        ];
    }

    /**
     * Lists all UserLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
        {
            if(Yii::$app->request->get('select'))//查询
            { 
                if (!empty(Yii::$app->request->post()))
                {
                    $data = \Yii::$app->request->post('UserLogSearch');
                }
                else
                {
                    $data = \Yii::$app->request->get('UserLogSearch');
                }
                $type = $data['type'];
                $username = $data['username'];
                $name = $data['name'];
                $start_time =! empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
                $end_time =! empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();
                $searchModel = new UserLogSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $dataProvider->query->orderBy('setting.add_time desc');
                $dataProvider->query->andWhere(['between','setting.add_time',$start_time,$end_time]);
                if(!empty($type))
                {
                    $dataProvider->query->andWhere(['type'=>$type]);   
                }
                if(!empty($username))
                {
                    if(is_numeric($username))
                    {
                        $user = User::findOne(['username'=>$username]);
                        if($user)
                        {
                            $dataProvider->query->andWhere(['user_id'=>$user->id]);
                        }
                        else
                        {
                            echo '<script>alert("用户不存在");history.back()</script>';
                        }
                    }
                    else
                    {
                            echo '<script>alert("用户名不正确");history.back()</script>';
                    }
                }
                if(!empty($name))
                {
                        $userdata = User::findOne(['name'=>$name]);
                        if($userdata)
                        {
                            $dataProvider->query->andWhere(['user_id'=>$userdata->id]);
                        }
                        else
                        {
                            echo '<script>alert("用户不存在");history.back()</script>';
                        }
                }
                $searchModel->type = $type;
                $searchModel->username = $username;
                $searchModel->name = $name;
                $searchModel->start_time = date('Y-m-d',$start_time);
                $searchModel->end_time = date('Y-m-d',$end_time);
                return $this->render('index', [
                        'searchModel' => $searchModel,            
                        'dataProvider' => $dataProvider, 
                    ]);
            }
            $dataProvider = new ActiveDataProvider([
                'query' => UserLog::find(),
            ]);
            $dataProvider->query->orderBy("id desc");
            $searchModel = new UserLogSearch();
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel'  => $searchModel
            ]); 
        }
        else
        {
            $this->redirect(['site/login']);
            return false;
        }
        
    }

    /**
     * Displays a single UserLog model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
