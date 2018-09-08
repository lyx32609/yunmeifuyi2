<?php

namespace backend\controllers;

use Yii;
use backend\models\User;
use backend\models\UserSign;
use backend\models\UserSignSearch;
use backend\models\Regions;
use backend\models\CompanyCategroy;
use backend\models\UserDepartment;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
use backend\models\UserWork;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');
/**
 * UserSignController implements the CRUD actions for UserSign model.
 */
class UserSignController extends BaseController
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
     * Lists all UserSign models.
     * @return mixed
     */
    public function actionIndex()
    {
        $rank = Yii::$app->user->identity->rank;//人员职务级别
        if(Yii::$app->request->get('select'))//查询
        { 
            if (!empty(Yii::$app->request->post()))
            {
                $data = \Yii::$app->request->post('UserSignSearch');
            }
            else
            {
                $data = \Yii::$app->request->get('UserSignSearch');
            }
            $type = $data['source_type'];
            $username = $data['username'];
            $name = $data['name'];
            if(($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = $data['company_id'];
            } 
            $department_id = empty($data['department']) ? "" : $data['department'];
            $start_time =! empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
            $end_time =! empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();           
            
            $searchModel = new UserSignSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->orderBy('setting.time desc');
            $dataProvider->query->andWhere(['between','setting.time',$start_time,$end_time]);

            $companyid = Yii::$app->user->identity->company_categroy_id;
            if(empty($company_id))//没有选公司
            {
                if(!in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
                {
                    if($rank == 30)//主公司经理
                    {
                        $child = CompanyCategroy::find()
                                ->select(["id"])
                                ->where(["fly"=>$companyid])
                                ->asArray()
                                ->all();
                        $count = count($child);
                        if($count > 0)
                        {
                            foreach($child as $k=>$v)
                            {
                                $company[$k] = $v['id'];
                                $company[$k+1] = $companyid;
                            }
                        }
                        else
                        {
                            $company[0] = $companyid;
                        }
                        $where_company = ['in',"company_categroy_id",$company];
                    }
                    if($rank == 3)//子公司经理
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
                    if(($rank == 4) && in_array("hr",$rules))//部门经理同时是hr
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    elseif(($rank == 4) && !in_array("hr",$rules))//部门经理非hr
                    {
                        $where_company = ["department_id" => Yii::$app->user->identity->department_id];
                    }
                    if (($rank == 1) && in_array("hr",$rules)){
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                }
                else
                {
                    $where_company = "";
                }
            }
            else
            {
                $where_company = ["company_categroy_id" => $company_id];
            }
            $userid = User::find()
                        ->select('id')
                        ->where($where_company)
                        ->asArray()
                        ->column();
            $dataProvider->query->andWhere(['in','user',$userid]);
            if(!empty($city))
            {
                $dataProvider->query->andWhere(['domain_id'=>$city]);
            }
            if(!empty($department_id)  && $department_id != '请选择部门' )
            {
                $dataProvider->query->andWhere(['department_id' => $department_id]);
            }
            if(!empty($type))
            {
                $dataProvider->query->andWhere(['source_type'=>$type]);   
            }
            if(!empty($username))
            {
                    $user = User::findOne(['username'=>$username]);
                    if($user)
                    {
                        $dataProvider->query->andWhere(['user'=>$user->id]);
                    }
                    else
                    {
                        echo '<script>alert("用户不存在");history.back()</script>';
                    }
            }
            if(!empty($name))
            {
                    $userdata = User::find()->select(["id"])->where(['name'=>$name])->asArray()->all();
                    foreach($userdata as $k=>$v){
                        $user_id_data[] = $v['id'];
                    }
                    if($userdata)
                    {
                        $dataProvider->query->andWhere(['in','user',$user_id_data]);
                    }
                    else
                    {
                        echo '<script>alert("用户不存在");history.back()</script>';
                    }
            }
            $searchModel->source_type = $type;
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->start_time = date('Y-m-d',$start_time);
            $searchModel->end_time = date('Y-m-d',$end_time);
            if((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {
                $searchModel->area = $area;
                $searchModel->city = $city;
                $searchModel->company_id = $company_id;
            }
            // echo "<pre>";
            // print_r($dataProvider);
            // exit();
            $searchModel->department = $department_id;
            if((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {
                return $this->render('index', [
                    'searchModel' => $searchModel,            
                    'dataProvider' => $dataProvider,
                    'areaid' => $area,  
                ]);
            }
            else
            {
                return $this->render('index', [
                    'searchModel' => $searchModel,            
                    'dataProvider' => $dataProvider, 
                ]);
            }
        }
        elseif(Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post()))
            {
                $data = \Yii::$app->request->post('UserSignSearch');
            }else
            {
                $data = \Yii::$app->request->get('UserSignSearch');
            }
            $start_time =! empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
            $end_time =! empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();
            $type = $data['source_type'];
            $username = empty($data["username"]) ? "" : $data["username"];
            $name = empty($data["name"]) ? "" : $data["name"];
            $department_id = empty($data['department']) ? "" : $data['department'];
            $companyid = Yii::$app->user->identity->company_categroy_id;
            if(($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = empty($data["company_id"]) ? "" : $data["company_id"];
            }
            
            $model = UserSign::find()
            ->select('u.username,u.name,u.company_categroy_id,off_user_sign.type,off_user_sign.source_type,off_user_sign.time,off_user_sign.is_late,off_user_sign.is_late_time,longitude,latitude,path')
            ->leftJoin(User::tableName().' u',UserSign::tableName().'.user=u.id');
            if(!empty($type))
            {
                $model->andWhere('source_type=:type',[':type'=>$type]);
            }
            if(is_numeric($data['username']))
            {
                $user = User::findOne(['username'=>$username]);
                $model->andWhere('user=:user',[':user'=>$user->id]);
            }
            if(!empty($name))
            {
                $userdata = User::find()->select(["id"])->where(['name'=>$name])->asArray()->all();
                foreach($userdata as $k=>$v){
                        $user_id_data[] = $v['id'];
                    }
                $model->andWhere(["in",'user',$user_id_data]);
            }
            if(!empty($city))
            {
                $model->andWhere(['domain_id'=>$city]);
            }
            if(!empty($department_id) && $department_id != '请选择部门')
            {
                $model->andWhere(['department_id'=>$department_id]);
            }
            if(!empty($company_id))
            {
                $model->andWhere(['company_id'=>$company_id]);
            }
            if(empty($company_id))//没有选公司
            {
                if(!in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
                {
                    if($rank == 30)//主公司经理
                    {
                        $child = CompanyCategroy::find()
                                ->select(["id"])
                                ->where(["fly"=>$companyid])
                                ->asArray()
                                ->all();
                        $count = count($child);
                        if($count > 0)
                        {
                            foreach($child as $k=>$v)
                            {
                                $company[$k] = $v['id'];
                                $company[$k+1] = $companyid;
                            }
                        }
                        else
                        {
                            $company[0] = $companyid;
                        }
                        $where_company = ['in',"company_categroy_id",$company];
                    }
                    if($rank == 3)//子公司经理
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
                    if(($rank == 4) && in_array("hr",$rules))//部门经理同时是hr
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    elseif(($rank == 4) && !in_array("hr",$rules))//部门经理非hr
                    {
                        $where_company = ["department_id" => Yii::$app->user->identity->department_id];
                    }
                    if (($rank == 1) && in_array("hr",$rules)){
                        $where_company = ["company_categroy_id" => $companyid];
                    }


                }
                else
                {
                    $where_company = "";
                }
            }
            else
            {
                $where_company = ["company_categroy_id" => $company_id];
            }
            $userid = User::find()
                        ->select('id')
                        ->where($where_company)
                        ->asArray()
                        ->column();
            $model->andWhere(['between','off_user_sign.time',$start_time,$end_time])
            ->andWhere(['in','user',$userid])
            // ->orderBy('off_user_sign.time desc');
            ->orderBy('off_user_sign.user desc');
            $model = $model->asArray()->all();
            
            error_reporting(E_ALL);
            date_default_timezone_set('Asia/ShangHai');
            $objPHPExcel = new \PHPExcel();
            /* 以下是一些设置 ，什么作者  标题啊之类的 */
            $objPHPExcel->getProperties()->setCreator(Yii::$app->user->Identity->username)
            
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2003 XLS Test Document")
            ->setSubject("Office 2003 XLS Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2003 openxml php")
            ->setCategory("Test result file");
            $i = 1;
            
            $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
           // $objPHPExcel->getActiveSheet()->setTitle(isset($user)?$user->name.'打卡记录':'人员打卡记录');
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')考勤记录':'人员考勤记录');
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
                );
            //列头
            $i = 2;
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '用户名')
            ->setCellValue('B'.$i, '姓名')
            ->setCellValue('C'.$i, '考勤情况')
            ->setCellValue('D'.$i, '时间')
            ->setCellValue('E'.$i, '经度')
            ->setCellValue('F'.$i, '纬度')
            ->setCellValue('G'.$i, '考勤地址')
            ->setCellValue('H'.$i, '状态');
            //循环获取数据
            $i = 3;

            foreach ($model as $v) 
            {
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,$v['username'])
                ->setCellValue('B'.$i,$v['name'])
                ->setCellValue('C'.$i,$v['source_type'] == 1 ? '云管理' : "考勤机")
                ->setCellValue('D'.$i,date('Y-m-d H:i:s',$v['time']))
                ->setCellValue('E'.$i,$v['longitude'])
                ->setCellValue('F'.$i,$v['latitude'])
                ->setCellValue('G'.$i,$v['path']!=''?$v['path']:'')
                ->setCellValue('H'.$i,$v['is_late'] == 1 ? '迟到'.$v['is_late_time'].'分':($v['is_late'] == 2?'早退'.$v['is_late_time'].'分':'正常'))
                ;
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
            $objPHPExcel->setActiveSheetIndex(0);
            
            
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
                );
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $filename =isset($user)?$user->username.'('.$user->name.')考勤记录':'人员考勤记录表';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
        }
        $searchModel = new UserSignSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy('time desc');
        //非超级管理员
        $companyid = Yii::$app->user->identity->company_categroy_id;
        $where_company = "";
        if(!in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
        {
            if($rank == 30)//主公司经理
            {
                $child = CompanyCategroy::find()
                        ->select(["id"])
                        ->where(["fly"=>$companyid])
                        ->asArray()
                        ->all();
                $count = count($child);
                if($count > 0)
                {
                    foreach($child as $k=>$v)
                    {
                        $company[$k] = $v['id'];
                        $company[$k+1] = $companyid;
                    }
                }
                else
                {
                    $company[0] = $companyid;
                }
                $where_company = ['in',"company_categroy_id",$company];
            }
            if($rank == 3)//子公司或者部门经理
            {
                $where_company = ["company_categroy_id"=>$companyid];
            }
            $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
            if(($rank == 4) && in_array("hr",$rules))//部门经理同时是hr
            {
                $where_company = ["company_categroy_id" => $companyid];
            }
            elseif(($rank == 4) && !in_array("hr",$rules))//部门经理非hr
            {
                $where_company = ["department_id" => Yii::$app->user->identity->department_id];
            }

        }
        else
        {
            $where_company = "";
        }
        $userid = User::find()
                ->select('id')
                ->where($where_company)
                ->asArray()
                ->column();
        $dataProvider->query->andWhere(['in','user',$userid]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
   
    /**
     * Displays a single UserSign model.
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
     * Creates a new UserSign model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserSign();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserSign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed   
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $path = Yii::$app->request->post('UserSign')['path'];
            $is_late = Yii::$app->request->post('UserSign')['is_late'];
            $is_late_time = Yii::$app->request->post('UserSign')['is_late_time'];
            $model->path = $path;
            $model->is_late = $is_late;
            $model->is_late_time = $is_late_time;
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserSign model.
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
     * Finds the UserSign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserSign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserSign::findOne($id)) !== null) {
            // if(!in_array($model->user,self::$userIds))
            // {
            //     return '';
            // }
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /*获取城市——created by 付腊梅*/
    public function actionGetCity(){
        $provinceId = $_GET['id'];
        $regions = Regions::find()
        ->where(['p_region_id'=>$provinceId])
        ->asArray()
        ->all();
        $data = '';
        if (count($regions) > 0)
        {
            $data = "<option>请选择市</option>";
            foreach ($regions as $v)
            {
                $select = '';
                $data .= "<option value='" . $v['region_id'] .  "' $select  >" . $v['local_name'] . "</option>";
            }
        }
        else
        {
            $data .= "<option>暂无城市</option>";
        }
        return $data;
        
        
    }

    /*获取公司——created by 付腊梅*/
    public function actionGetCompany()
    {
        $area_id = $_GET['area_id'];
        $domain_id = $_GET['city_id'];
        $company = CompanyCategroy::findCompany($area_id,$domain_id)
                ->orderBy("id asc")
                ->asArray()
                ->all();
        $data = '';
        if (count($company) > 0)
        {
            $data = "<option>请选择公司</option>";
            foreach ($company as $v)
            {
                $select = '';
                $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['name'] . "</option>";
            }
        }
        else
        {
            $data .= "<option>暂无公司</option>";
        }
        return $data;
    }

    /*获取部门——created by 付腊梅*/
    public function  actionGetDepartment()
    {
        $area_id = $_GET['area_id'];
        $domain_id = $_GET['domain_id'];
        $company_id = $_GET['company_id'];
        $department = UserDepartment::findDepartment($area_id,$domain_id,$company_id)
                    ->orderBy("priority desc")
                    ->asArray()
                    ->all();
        $data = '';
        if (count($department) > 0)
        {
            if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {
                $data = "<option>请选择部门</option>";
                foreach ($department as $v)
                {
                    $select = '';
                    $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['name'] . "</option>";
                }
            }
            else
            {
                foreach ($department as $v)
                {
                    $select = '';
                    $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['name'] . "</option>";
                }
            }

        }
        else
        {
            $data .= "<option>暂无部门</option>";
        }
        return $data;
    }

    /*其他地方用 签到记录不用*/
    public function actionProvince(){
        $provinceId = $_GET['id'];
        
        $regions = Regions::find()
        ->where(['p_region_id'=>$provinceId])
        ->asArray()
        ->all();
        $data = '';
        
        if (count($regions) > 0) {
            $data = "<option>全部</option>";
            foreach ($regions as $v) {
                $select = '';
                $data .= "<option value='" . $v['region_id'] .  "' $select  >" . $v['local_name'] . "</option>";
            }
        } else {
            $data .= "<option>全部</option>";
        }
        return $data;
    }
            /**
=======
        /**
>>>>>>> .theirs
     * @return string
     * 按照登录人的角色等级获取公司
     */
    public function actionGetCompanyName()
    {
        $area_id = $_GET['area_id'];
        $domain_id = $_GET['city_id'];
        $uid = \Yii::$app->user->id;
        $company_id = User::find()
            ->select('company_categroy_id')
            ->where(['id'=>$uid])
            ->asArray()
            ->one()
        ;
        if(in_array($uid,Yii::$app->params['through'])){
            $company = CompanyCategroy::findCompany($area_id,$domain_id)
                ->orderBy("id asc")
                ->asArray()
                ->all();

        }else{
            $company = CompanyCategroy::find()
                ->select(['id','name'])
                ->where(['id'=>$company_id['company_categroy_id'],'area_id'=>$area_id,'domain_id'=>$domain_id])
                ->orderBy("id asc")
                ->asArray()
                ->all();
        }
        $data = '';
        if (count($company) > 0)
        {
            $data = "<option>请选择公司</option>";
            foreach ($company as $v)
            {
                $select = '';
                $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['name'] . "</option>";
            }
        }
        else
        {
            $data .= "<option>暂无公司</option>";
        }
        return $data;
    }
    /**
     * 按照登录人的角色等级获取部门名称和ID(车辆GPS设备号和车牌号绑定时用)
     * @return string
     *
     */
    public function  actionGetDepartmentName()
    {
        $area_id = $_GET['area_id'];
        $domain_id = $_GET['city_id'];
        $company_id = $_GET['company_id'];
        $user_id = \Yii::$app->user->id;
        if(in_array($user_id,Yii::$app->params['through'])){
            $department = UserDepartment::findDepartment($area_id,$domain_id,$company_id)
                ->orderBy("priority desc")
                ->asArray()
                ->all();
        }else{
            $department = UserDepartment::find()
                ->where(['company'=>$company_id,'domain_id'=>$domain_id])
                ->orderBy("priority desc")
                ->asArray()
                ->all();
        }

        $data = '';
        if (count($department) > 0)
        {
            if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {
                $data = "<option>请选择部门</option>";
                foreach ($department as $v)
                {
                    $select = '';
                    $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['name'] . "</option>";
                }
            }
            else
            {
                foreach ($department as $v)
                {
                    $select = '';
                    $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['name'] . "</option>";
                }
            }

        }
        else
        {
            $data .= "<option>暂无部门</option>";
        }
        return $data;
    }
}
