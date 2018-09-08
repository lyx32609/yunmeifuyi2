<?php

namespace backend\controllers;

use Yii;
use backend\models\CompanyCategroy;
use backend\models\CompanyCategroySearch;
use backend\models\CompanyCategroyReview;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompanyCategroyController implements the CRUD actions for CompanyCategroy model.
 */
class CompanyController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;
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
     * Lists all CompanyCategroy models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CompanyCategroySearch();
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
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
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
    
    public function actionCheck($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }else{
            return $this->render('check', [
                'model' => $model,
            ]);
        }
    }

    public function actionSaveCheck()
    {
        //echo $_GET['id'];
        // echo "<pre>";
        // print_r($_POST);
        // echo "</pre>";
        $data = $_POST['CompanyCategroy'];
        $data['name']= empty($data['name']) ? "0" :  $data['name'] ;
        $data['status']= empty($data['status']) ? "0" :  $data['status'] ;
        $data['createtime']= empty($data['createtime']) ? "0" :  $data['createtime'] ;
        $data['phone']= empty($data['phone']) ? "0" :  $data['phone'] ;
        $data['area_id']= empty($data['area_id']) ? "0" :  $data['area_id'] ;
        $data['domain_id']= empty($data['domain_id']) ? "0" :  $data['domain_id'] ;
        $data['fly']= empty($data['fly']) ? "0" :  $data['fly'] ;
        $data['type']= empty($data['type']) ? "0" :  $data['type'] ;
        $data['review'] = empty($data['review']) ? "0" : $data['review'];
        $data['license_num']= empty($data['license_num']) ? "0" :  $data['license_num'] ;
        $data['register_money']= empty($data['register_money']) ? "0" :  $data['register_money'] ;
        $data['business']= empty($data['business']) ? "0" :  $data['business'] ;
        $data['business_ress'] = empty($data['business_ress']) ? "0" :  $data['business_ress'] ;
        $data['staff_num']= empty($data['staff_num']) ? "0" :  $data['staff_num'] ;
        $data['acting']= empty($data['acting']) ? "0" :  $data['acting'] ;
        $data['proxy_level']= empty($data['proxy_level']) ? "0" :  $data['proxy_level'] ;
        $data['service_area']= empty($data['service_area']) ? "0" :  $data['service_area'] ;
        $data['distribution_merchant']= empty($data['distribution_merchant']) ? "0" :  $data['distribution_merchant'] ;
        $data['distribution_car']= empty($data['distribution_car']) ? "0" :  $data['distribution_car'] ;
        $data['distribution_staff']= empty($data['distribution_staff']) ? "0" :  $data['distribution_staff'] ;
        $data['goods_num'] = empty($data['goods_num']) ? "0" :  $data['goods_num'] ;
        $data['failure'] = empty($data['failure']) ? "0" : $data['failure'];
        $data['goods_type'] = empty($data['goods_type']) ? "0" :  $data['goods_type'] ;
        $data['service_type'] = empty($data['service_type']) ? "0" :  $data['service_type'] ;
        $data['product_type'] = empty($data['product_type']) ? "0" :  $data['product_type'] ;
        $data['salas_business'] = empty($data['salas_business']) ? "0" :  $data['salas_business'] ;
        $data['license_image'] = empty($data['license_image']) ? "0" :  $data['license_image'] ;
        $data['user_image_negative'] = empty($data['user_image_negative']) ? "0" :  $data['user_image_negative'] ;
        $data['user_image_positive'] = empty($data['user_image_positive']) ? "0" :  $data['user_image_positive'] ;
        $model = new CompanyCategroyReview();
        $model->name = $data['name'];
        $model->status = $data['status'];
        $model->createtime = $data['createtime'];
        $model->phone = $data['phone'];
        $model->area_id = $data['area_id'];
        $model->domain_id = $data['domain_id'];
        $model->review = $data['review'];
        $model->license_num = $data['license_num'];
        $model->register_money = $data['register_money'];
        $model->business = $data['business'];
        $model->business_ress = $data['business_ress'];
        $model->staff_num = $data['staff_num'];
        $model->acting = $data['acting'];
        $model->proxy_level = $data['proxy_level'];
        $model->service_area = $data['service_area'];
        $model->distribution_merchant = $data['distribution_merchant'];
        $model->distribution_car = $data['distribution_car'];
        $model->distribution_staff = $data['distribution_staff'];
        $model->goods_num = $data['goods_num'];
        $model->failure = $data['failure'];
        $model->goods_type = $data['goods_type'];
        $model->service_type = $data['service_type'];
        $model->product_type = $data['product_type'];
        $model->salas_business = $data['salas_business'];
        $model->license_image = $data['license_image'];
        $model->user_image_negative = $data['user_image_negative'];
        $model->user_image_positive = $data['user_image_positive'];
        $model->company_id = $_GET['id'];
        $model_company = $this->findModel($_GET['id']);
        if($model->save())
        {
            if(($data['name'] == "0") && ($data['status'] == "0") && ($data['createtime'] == "0")&& ($data['phone'] == "0")&& ($data['area_id'] == "0")&& ($data['domain_id'] == "0")&& ($data['fly'] == "0")&& ($data['type'] == "0")&& ($data['review'] == "0")&& ($data['license_num'] == "0")&& ($data['register_money'] == "0")&& ($data['business'] == "0")&& ($data['business_ress'] == "0")&& ($data['staff_num'] == "0")&& ($data['acting'] == "0")&& ($data['proxy_level'] == "0")&& ($data['service_area'] == "0")&& ($data['distribution_merchant'] == "0")&& ($data['distribution_car'] == "0")&& ($data['distribution_staff'] == "0")&& ($data['goods_num'] == "0")&& ($data['failure'] == "0")&& ($data['goods_type'] == "0")&& ($data['service_type'] == "0")&& ($data['product_type'] == "0")&& ($data['salas_business'] == "0")&& ($data['license_image'] == "0")&& ($data['user_image_negative'] == "0")&& ($data['user_image_positive'] == "0")&& ($data['name'] == "0"))
            {
                $model_company->review = 2;
                $model_company->save();
            }
            else
            {
                $model_company->review = 3;
                $model_company->save();
            }
            
            echo "<script>alert('审核成功');window.location.href='/company/index';</script>";
        }
        else
        {
            // $model_company->review = 3;
            // $model_company->save();
            echo "<script>alert('审核失败，请重新审核！');window.location.href='/company/index';</script>";
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

    public function viewt($id)
    {
        
    }
}
