<?php

namespace backend\controllers;

use Yii;
use backend\models\UserGroup;
use backend\models\UserGroupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\UserDepartment;
use official\api\hr\DepartmentDomainApi;

/**
 * UserGroupController implements the CRUD actions for UserGroup model.
 */
class UserGroupController extends BaseController
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
     * Lists all UserGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserGroupSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy('priority desc');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserGroup model.
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
     * Creates a new UserGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserGroup();
        if($_POST){
        	$model->department_id = $_POST['UserGroup']['department_id'];  //？？
        	$model->load(Yii::$app->request->post());
        	$domainid = UserDepartment::find()->select('domain_id')->where(['id'=>$model->department_id])->asArray()->one();
        	$model->domain_id =  $domainid['domain_id'];
        }
        
        if (Yii::$app->request->post() && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($_POST){
        	$model->department_id = $_POST['UserGroup']['department_id'];  //？？
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserGroup model.
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
     * Finds the UserGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    //根据传入地区id 获取部门
   public function actionDepartment()
    {
    	$id = $_GET['id'];
    	$branches = UserDepartment::find()
				    	->where(['domain_id' => $id])
				    	->asArray()
				    	->all();
    	$data = '';
    	if(isset($_GET['pid'])){
    		$selectid = UserGroup::find()
    		->where(['id' => $_GET['pid']])
    		->asArray()
    		->one();
    	}
    	if (count($branches) > 0) {
    		foreach ($branches as $branche) {
    			if(isset($selectid)){
	    			if($selectid['department_id'] == $branche['id']){
	    				$select = 'selected = "selected"';
	    			}else{
	    				$select = '';
	    			}
    			}else{
    				$select = '';
    			}
    			$data .= "<option value='" . $branche['id'] .  "' $select  >" . $branche['name'] . "</option>";
    		}
    	} else {
    		$data .= "<option>-</option>";
    	}
    	return $data;
    } 
    
    

    //根据传入地区id 获取部门
    public function actionDepartmentall()
    {
        $id = $_GET['id'];
        $branches = UserDepartment::find()
        ->where(['domain_id' => $id])
        ->asArray()
        ->all();
        $data = '';
        if(isset($_GET['pid'])){
            $selectid = UserGroup::find()
            ->where(['id' => $_GET['pid']])
            ->asArray()
            ->one();
        }
        if (count($branches) > 0) {
            foreach ($branches as $branche) {
                if(isset($selectid)){
                    if($selectid['department_id'] == $branche['id']){
                        $select = 'selected = "selected"';
                    }else{
                        $select = '';
                    }
                }else{
                    $select = '';
                }
                $data .= "<option value='" . $branche['id'] .  "' $select  >" . $branche['name'] . "</option>";
            }
        } else {
            $data .= "<option>-</option>";
        }
        return $data;
    }
    
    
}
