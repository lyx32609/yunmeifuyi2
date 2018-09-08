<?php

namespace backend\controllers;

use Yii;
use backend\models\UserDepartment;
use backend\models\UserDepartmentSearch;
use backend\models\Regions;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserDepartmentController implements the CRUD actions for UserDepartment model.
 */
class UserDepartmentController extends BaseController
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
     * Lists all UserDepartment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserDepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy('priority desc');
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserDepartment model.
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
     * Creates a new UserDepartment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserDepartment();
        if(Yii::$app->request->Post()){
            $companyid=Yii::$app->user->identity->company_categroy_id;
            $model->load(Yii::$app->request->post());
            $model->company =  $companyid;
            $model->priority = !empty(Yii::$app->request->post('UserDepartment')['priority'] ) ? Yii::$app->request->post('UserDepartment')['priority'] : 0;
            $model->domain_id = Yii::$app->request->post('UserDepartment')['domain_id'];
        }
        
        if ( Yii::$app->request->Post() && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserDepartment model.
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
            $regin = Regions::find()->select('p_region_id')->where(['region_id'=>$model->domain_id])->asArray()->one();
            $model->province = $regin['p_region_id'];
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserDepartment model.
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
     * Finds the UserDepartment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserDepartment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserDepartment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
