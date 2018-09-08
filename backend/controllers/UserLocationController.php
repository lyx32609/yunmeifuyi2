<?php

namespace backend\controllers;

use Yii;
use backend\models\User;
use backend\models\UserSign;
use backend\models\UserSignSearch;
use backend\models\UserDomain;
use backend\models\UserLocation;
use backend\models\UserBusiness;
use backend\models\UserBusinessNotes;
use backend\models\UserLocationSearch;
use backend\models\UserDepartment;
use backend\models\Members;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
use backend\models\CompanyCategroy;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');
/**
 * UserSignController implements the CRUD actions for UserSign model.
 */
class UserLocationController extends BaseController
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
       $userlocation =  UserLocation::find()
                        ->where(['username'=>null,'reasonable'=>null])
                        ->andwhere(['<>','shop_id',0])
                        ->asArray()
                        ->count();
       $id = Yii::$app->user->identity->id;
       $isadmin = in_array($id, Yii::$app->params['through']);
       /*同步员工定位数据*/
       if($userlocation > 0 && !$isadmin)
       {
           return $this->render('synchronization');
       }
       if(Yii::$app->request->get('tongbu'))
       {
           return $this->render('synchronization');
       }
       $rank = Yii::$app->user->identity->rank;//人员职务级别
       /*查询*/
        if(Yii::$app->request->get('select'))
        {
            if (!empty(Yii::$app->request->post())){
                $data=\Yii::$app->request->post('UserLocationSearch');
            }else{
                $data=\Yii::$app->request->get('UserLocationSearch');
            }
            
            $reasonable = $data['reasonable'];//是否合理
            $shopname = $data['shopname'];//店铺名
            $personalusername = $data['personalusername'];//员工名
            $type = $data["type"];//定位来源
            if(($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];//省
                $city = $data['city'];//市
                $company_id = $data['company_id'];//公司
            }
            $department =  empty($data['department']) ? "" : $data['department'];//部门
            $start_time =! empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
            $end_time =! empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();           
            $searchModel = new UserLocationSearch();
            $data = $searchModel->search(Yii::$app->request->queryParams);
            $data->query->orderBy('time desc');
            $data->query->andWhere(['between','setting.time',$start_time,$end_time]);
            $companyid = Yii::$app->user->identity->company_categroy_id;//登录人所在公司
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
                        ->select('username')
                        ->where($where_company)
                        ->asArray()
                        ->column();
            $data->query->andWhere(['in','user',$userid]);
            if(!empty($shopname))
            {
                $data->query->andWhere(['like', 'setting.name', $shopname]);
            }
            if(!empty($reasonable))
            {
                $reasonablers =  $reasonable == 1 ? '合理' : '不合理';
                $data->query->andWhere(['reasonable'=>$reasonablers]);
            }
            if(!empty($city))
            {
                $data->query->andWhere(['domain_id'=>$city]);
            }
            if(!empty($department) && $department != '请选择部门')
            {
                $data->query->andWhere(['department_id'=>$department]);
            }
            if(!empty($type))
            {
                $data->query->andWhere(['type'=>$type]);   
            }
            if(!empty($personalusername))
            {
                $data->query->andWhere(['like','user.name',$personalusername]);
            }
            
            $searchModel->reasonable = $reasonable;
            $searchModel->shopname = $shopname;
            $searchModel->personalusername = $personalusername;
            $searchModel->start_time = date('Y-m-d',$start_time);
            $searchModel->end_time = date('Y-m-d',$end_time);
            if((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {
                $searchModel->area = $area;
                $searchModel->city = $city;
                $searchModel->company_id = $company_id;
            }
            $searchModel->type = $type;
            $searchModel->department = $department;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $data,
            ]);

        }
        elseif(Yii::$app->request->get('export'))
        {
            if(!empty(Yii::$app->request->post()))
            {
                $data = \Yii::$app->request->post('UserLocationSearch');
            }else
            {
                $data = \Yii::$app->request->get('UserLocationSearch');
            }
            $start_time = !empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
            $end_time = !empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();
            $shopname = $data['shopname'];
            $reasonable = $data['reasonable'];
            $companyid = Yii::$app->user->identity->company_categroy_id;
            if(($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params   ['through']))
            {   
                $area = $data['area'];
                $city = $data['city'];
                $company_id = empty($data["company_id"]) ? "" : $data["company_id"];
            }
            $type = $data['type'];
            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            // exit();
            $department = empty($data['department']) ? "" : $data['department'];
            $personalusername = $data['personalusername'];
            $model = UserLocation::find()
            ->select('ub.customer_tel,ubt.followup_text,a.region,u.name,shop_id,bing_id,p.name as customerName,p.longitude,p.latitude,p.time,type,p.domain,belong,reasonable')
            ->from(UserLocation::tableName().' AS p')
            ->leftJoin(User::tableName().' u','p.user=u.username')
            ->leftJoin(UserDomain::tableName().' a','p.domain=a.domain_id')
            ->leftJoin(UserBusiness::tableName().' ub','ub.id=p.shop_id')
            ->leftJoin(UserBusinessNotes::tableName().' ubt','ubt.business_id=p.shop_id and  ubt.staff_num = p.user');
            if(!empty($shopname))
            {
            	$model->andWhere(['like', 'p.name', $shopname]);
            }
            if(!empty($reasonable))
            {
                $reasonablers =  $reasonable == 1 ? '合理' : '不合理';
                $model->andWhere(['p.reasonable'=>$reasonablers]);
            }
            if(($type == 0) || ($type == 1))
            {
                $model->andWhere(['p.type'=>$type]);
            }
            if(!empty($city))
            {
                $model->andWhere(['p.domain'=>$city]);
            }
            if(!empty($department) && $department != '请选择部门')
            {
                $model->andWhere(['u.department_id'=>$department]);
            }
            if(!empty($personalusername))
            {
                $model->andWhere(['like','u.name',$personalusername]);
            }
            $companyid = Yii::$app->user->identity->company_categroy_id;//登录人所在公司
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
                        ->select('username')
                        ->where($where_company)
                        ->asArray()
                        ->column();
            $model->andWhere(['between','p.time',$start_time,$end_time])
                  ->andWhere(["in","user",$userid])
                  ->orderBy('p.time desc');
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
            
            $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
           // $objPHPExcel->getActiveSheet()->setTitle(isset($user)?$user->name.'打卡记录':'人员打卡记录');
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, '员工定位记录表'.date('Y-m-d H:i:s',time()));
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
            ->setCellValue('A'.$i, '店铺id')
            ->setCellValue('B'.$i, '店铺名称')
            ->setCellValue('C'.$i, '经度')
            ->setCellValue('D'.$i, '纬度')
            ->setCellValue('E'.$i, '定位来源')
            ->setCellValue('F'.$i, '地区')
            ->setCellValue('G'.$i, '定位类型')
            ->setCellValue('H'.$i, '员工姓名')
            ->setCellValue('I'.$i, '是否合理')
            ->setCellValue('J'.$i, '时间')
                ;
            //循环获取数据
            $i = 3;
            		
            foreach ($model as $v) {
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,$v['shop_id'])
                ->setCellValue('B'.$i,$v['customerName'])
                ->setCellValue('C'.$i,$v['longitude'])
                ->setCellValue('D'.$i,$v['latitude'])
                ->setCellValue('E'.$i,$v['type']==0?'业务回访':($v['type']==1?'新增业务':'未记录'))
                ->setCellValue('F'.$i,$v['region']== "" ? '未记录' : $v['region'])
                ->setCellValue('G'.$i,$v['belong']==1?'采购商':($v['type']==2?'代理商':'默认业务跟进'))
                ->setCellValue('H'.$i,$v['name'])
                ->setCellValue('I'.$i,$v['reasonable'])
                ->setCellValue('J'.$i,date('Y-m-d H:i:s',$v['time']))
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
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);//打卡记录':'员工定位记录表';
            if(isset($personalusername))
            {
                $filename = $personalusername.'定位记录表'.date('Y-m-d',time());
            }
            {
                $filename = '员工定位记录表'.date('Y-m-d',time());
            }
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        $searchModel = new UserLocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $where_company = "";
        $companyid = Yii::$app->user->identity->company_categroy_id;
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
                ->select('username')
                ->where($where_company)
                ->asArray()
                ->column();
        $dataProvider->query->andWhere(['in','user',$userid]);
        $dataProvider->query->orderBy('time desc');
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
            if(!in_array($model->user,self::$userIds))
            {
                return '';
            }
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    //根据传入地区id 获取部门
    public function actionDepartment()
    {
        $id = Yii::$app->user->identity->id;
        $rank = Yii::$app->user->identity->rank;
        $company = Yii::$app->user->identity->company_categroy_id;
        $isadmin = in_array($id, Yii::$app->params['through']);
        $id = $_GET['id'];
        if(!$isadmin)
        {
            if($id == 0)
            {
                $where = ['company'=>$company];
            }else{
                $where = ['domain_id' => $id,'company'=>$company];
            }
        }
        
        $branches = UserDepartment::find()
                    ->where($where)
                    ->asArray()
                    ->all();
        $data = '';
        if(count($branches) > 0)
        {
            $data = "<option>全部</option>";
            foreach ($branches as $branche)
            {
                 $select = '';
                $data .= "<option value='" . $branche['id'] .  "' $select  >" . $branche['name'] . "</option>";
            }
        }
        else
        {
            $data .= "<option>全部</option>";
        }
        return $data;
    }
    
    public function actionSynchronization(){        
        if(Yii::$app->request->post('tongbu'))
        {
            $num = Yii::$app->request->post('num');
            $timedata['stime'] = date('Y-m-d H:i:s',time());
            $user_location = new UserLocation();
            $ss = $user_location::find()
            ->where(['<>','shop_id',0])
            ->andWhere(['username'=>null,'reasonable'=>null])
            ->limit($num)
            ->orderBy('id desc')->asArray()->all();
            $arrdata = array();
            foreach ($ss as $v)
            {
                $shopId = $v['shop_id'];
                if($shopId == 0)
                {
                    continue;
                }
                $shop = Yii::$app->api->request('basic/getMember',['member_id'=>$shopId]);
                if($shop['ret'] == 100)
                {
                    $username = User::find()->select('name')->where(['username'=>$v['user']])->asArray()->one();
                    $columns['username'] = $username['name'];
                    $columns['reasonable'] = '不存在';
                }
                else
                {
                    if(!isset($shop[0]))
                    {
                        continue;
                    }
                    $point1 = array('lat' => $shop[0]['latitude'], 'long' => $shop[0]['longitude']);
                    $point2 = array('lat' => $v['latitude'], 'long' => $v['longitude']);
                    $distance = $this->getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
                    if($distance<80)
                    {
                        $columns['reasonable'] = '合理';
                    }
                    else
                    {
                        $columns['reasonable'] = '不合理';
                    }
                    $username = User::find()->select('name')->where(['username'=>$v['user']])->asArray()->one();
                    $columns['username'] = $username['name'];
                    $arrdata[$v['id']]['reasonable'] = $columns['reasonable'];
                    $arrdata[$v['id']]['username'] = $username['name'];
                }
                \Yii::$app->dbofficial->createCommand()->update('off_user_location', $columns,['id'=>$v['id']])->execute();
            
                //批量修改s
/*                 $reasonable = '"'.$columns['reasonable'].'"';
                $reasonablesql .= sprintf("WHEN %d THEN %s ", $v['id'], $reasonable);
                $username = '"'.$columns['username'].'"';
                $usernamesql .= sprintf("WHEN %d THEN %s ", $v['id'], $username); */
                //批量修改e
            }
            //批量修改s
/*             $ids = implode(',', array_keys($arrdata));
            $sql = "UPDATE off_user_location SET reasonable = CASE id ";
            $sql .= $reasonablesql; */
            /*         foreach ($arrdata as $id => $ordinal) {
             $reasonable = '"'.$ordinal['reasonable'].'"';
             $sql .= sprintf("WHEN %d THEN %s ", $id, $reasonable);
             } */
/*             $sql .= 'END,username = CASE id ';
            $sql .= $usernamesql; */
            /*         foreach ($arrdata as $id => $ordinal) {
             $username = '"'.$ordinal['username'].'"';
             $sql .= sprintf("WHEN %d THEN %s ", $id, $username);
             } */
/*             $sql .= "END WHERE id IN ($ids)";
            Yii::$app->dbofficial->createCommand($sql)->execute(); */
            //批量修改e
            
/*             $timedata['etime'] = date('Y-m-d H:i:s',time());
            return $timedata; */
            Yii::$app->session->setFlash('success','同步完成');
/*             $searchModel = new UserLocationSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->orderBy('time desc');
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]); */
            $this->redirect('/user-location/index');
        }
        
        return $this->render('synchronization');
    }
    
    //获取两个经纬度之间的距离
        public function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
        {
        
            $theta = $longitude1 - $longitude2;
            $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
            $miles = acos($miles);
            $miles = rad2deg($miles);
            $miles = $miles * 60 * 1.1515;
            $kilometers = $miles * 1.609344;
            $meters = $kilometers * 1000;
            return $meters;
        }
    
    
}
