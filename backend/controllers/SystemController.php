<?php

namespace backend\controllers;

use Yii;
use backend\models\CompanyReview;
use backend\models\CompanyReviewSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SystemController implements the CRUD actions for CompanyReview model.
 */
class SystemController extends Controller
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
     * Lists all CompanyReview models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $model = new CompanyReview();
        $data = $model::find()->asArray()->one();
        
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'review' => $data['review'],
        ]);
    }

    /**
     * Displays a single CompanyReview model.
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
     * Creates a new CompanyReview model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CompanyReview();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CompanyReview model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $review = $_GET['review'];
        //echo $review;exit();
        $reviewStatus = $review == "1" ? 2 : 1;
/*         $reviewModel = new CompanyReview();
        $reviewModel->review = $reviewStatus;
        $reviewModel->save(); */

        $reviewModel = CompanyReview::find()->where(['id'=>1])->one(); 
        $reviewModel->review = $reviewStatus;
        $reviewModel->save();   //保存
        
        
/*         $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        } */
    }

    /**
     * Deletes an existing CompanyReview model.
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
     * Finds the CompanyReview model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CompanyReview the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompanyReview::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
