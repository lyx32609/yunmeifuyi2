<?php

namespace backend\controllers;

use Yii;
use backend\models\CompanyCategroy;
use backend\models\ChildCompanyCategroySearch;
use backend\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChildCompanyController implements the CRUD actions for CompanyCategroy model.
 */
class ChildCompanyController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::  className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CompanyCategroy models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChildCompanyCategroySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy("id desc");

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CompanyCategroy model.
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
     * Creates a new CompanyCategroy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CompanyCategroy();
        $model_user = new User();
        if ($model->load(Yii::$app->request->post()))
        {
            $model->createtime = time();
            $post_data = Yii::$app->request->post();
            $company_data = $post_data['CompanyCategroy'];
            $model->name = $company_data['name'];
            $model->status = $company_data['status'];
            $model->createtime = time();
            $model->phone = $company_data['phone'];
            $model->area_id = $company_data['area_id'];
            $model->domain_id = $company_data['domain_id'];
            $model->fly = $company_data['fly'];
            $company_data_fly = CompanyCategroy::find()->select(["type"])->where(["id"=>$company_data['fly']])->one();
            $model->type = $company_data_fly['type'];
            $model->review = 2;
            $model->license_num = $company_data['license_num'];
            $model->register_money = $company_data['register_money'];
            $model->business = $company_data['business'];
            $model->business_ress = $company_data['business_ress'];
            $model->staff_num = $company_data['staff_num'];
            $model->acting = $company_data['acting'];
            $model->proxy_level = $company_data['proxy_level'];
            $model->distribution_merchant = $company_data['distribution_merchant'];
            $model->distribution_car = $company_data['distribution_car'];
            $model->distribution_staff = $company_data['distribution_staff'];
            $model->goods_num = $company_data['goods_num'];
            $model->failure = $company_data['failure'];
            $model->goods_type = $company_data['goods_type'];
            $model->service_type = $company_data['service_type'];
            $model->product_type = $company_data['product_type'];
            $model->salas_business = $company_data['salas_business'];
            $model->license_image = $company_data['license_image'];
            $model->user_image_negative = $company_data['user_image_negative'];
            $model->user_image_positive = $company_data['user_image_positive'];
            if($model->save())//企业信息保存成功
            {
                $user_data = $post_data['User'];
                $company_data_id = CompanyCategroy::find()->select(["id"])->where(["name"=>$company_data['name']])->one();
                $user_data['password'] = md5($user_data['password']);
                $model_user->staff_code = 0;
                $model_user->username = $user_data['username'];
                $model_user->password = $user_data['password'];
                $model_user->phone = $company_data['phone'];
                $model_user->name = $user_data['name'];
                $model_user->domain_id = $company_data['domain_id'];
                $model_user->group_id = 0;
                $model_user->department_id = 0;
                $model_user->is_select = 0;
                $model_user->rank = 3;
                $model_user->company_categroy_id = $company_data_id['id'];
            }
            if($model->save() && $model_user->save())
            {
                return $this->redirect(['view', 'id' => $model->id]);
            }
            else
            {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else 
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CompanyCategroy model.
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
     * Deletes an existing CompanyCategroy model.
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
     * Finds the CompanyCategroy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CompanyCategroy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompanyCategroy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
