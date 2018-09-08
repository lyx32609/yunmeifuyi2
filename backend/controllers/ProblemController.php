<?php

namespace backend\controllers;

use Yii;
use backend\models\Problem;
use backend\models\ProblemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
use backend\models\User;
use backend\models\CompanyCategroy;
use backend\models\UserDepartment;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');
/**
 * ProblemController implements the CRUD actions for problem model.
 */
class ProblemController extends Controller
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
                    'delete' => ['POST','GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all problem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $rank = Yii::$app->user->identity->rank;//登录人职务级别
        if(Yii::$app->request->get('select'))//查询
        {
            if (!empty(Yii::$app->request->post()))
            {
                $data = \Yii::$app->request->post('ProblemSearch');
            }
            else
            {
                $data = \Yii::$app->request->get('ProblemSearch');
            }

            $problem_title = $data["problem_title"];
            $priority = $data["priority"];
            $user_name = $data["user_name"];
            $start_time =! empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
            $end_time =! empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();
            if(($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = $data['company_id'];
            } 
            $department_id = empty($data['department_id']) ? "" : $data['department_id'];
            $searchModel = new ProblemSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->orderBy('create_time desc');
            $dataProvider->query->andWhere(['between','create_time',$start_time,$end_time]);
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
            $dataProvider->query->andWhere(['in','user_id',$userid]);
            if(!empty($city))
            {
                $dataProvider->query->andWhere(['domain_id'=>$city]);
            }
            if(!empty($department_id)  && $department_id != '请选择部门' )
            {
                $dataProvider->query->andWhere(['department_id' => $department_id]);
            }
            if(!empty($priority))
            {
                $dataProvider->query->andWhere(['priority'=>$priority]);   
            }
            if(!empty($problem_title))
            {
                $dataProvider->query->andWhere(["like",'problem_title',$problem_title]);
            }
            if(!empty($username))
            {
                    $userdata = User::findOne(['user_name'=>$user_name]);
                    if($userdata)
                    {
                        $dataProvider->query->andWhere(["like",'user_name',$user_name]);
                    }
                    else
                    {
                        echo '<script>alert("用户不存在");history.back()</script>';
                    }
             }
            $dataProvider->query->orderBy("id desc");
            $searchModel->priority = $priority;
            $searchModel->user_name = $user_name;
            $searchModel->problem_title = $problem_title;
            $searchModel->start_time = date('Y-m-d',$start_time);
            $searchModel->end_time = date('Y-m-d',$end_time);
            if((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {
                $searchModel->area = $area;
                $searchModel->city = $city;
                $searchModel->company_id = $company_id;
            }
            $searchModel->department_id = $department_id;
            return $this->render('index', [
                    'searchModel' => $searchModel,            
                    'dataProvider' => $dataProvider, 
                ]);
        }
        elseif(Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post()))
            {
                $data = \Yii::$app->request->post('ProblemSearch');
            }
            else
            {
                $data = \Yii::$app->request->get('ProblemSearch');
            }
            $problem_title = $data["problem_title"];
            $priority = $data["priority"];
            $user_name = $data["user_name"];
            $start_time =! empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
            $end_time =! empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();
            $companyid = Yii::$app->user->identity->company_categroy_id;
            if(($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = $data['company_id'];
            } 
            $department_id = empty($data['department_id']) ? "" : $data['department_id'];
            $model = Problem::find()
            ->select('off_problem.*,u.domain_id,u.department_id,u.name')
            ->leftJoin(User::tableName().' u',Problem::tableName().'.user_id=u.id');
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
                        ->select('id')
                        ->where($where_company)
                        ->asArray()
                        ->column();

            if(!empty($city))
            {
                $model->andWhere(['domain_id'=>$city]);
            }
            if(!empty($department_id)  && $department_id != '请选择部门' )
            {
                $model->andWhere(['department_id' => $department_id]);
            }
            if(!empty($priority))
            {
                $model->andWhere(['priority'=>$priority]);   
            }
            if(!empty($problem_title))
            {
                $model->andWhere(["like",'problem_title',$problem_title]);
            }
            if(!empty($username))
            {
                    $userdata = User::findOne(['user_name'=>$user_name]);
                    if($userdata)
                    {
                        $model->andWhere(["like",'user_name',$user_name]);
                    }
                    else
                    {
                        echo '<script>alert("用户不存在");history.back()</script>';
                    }
             }
            $model->andWhere(['between','off_problem.create_time',$start_time,$end_time])
            ->andWhere(['in','user_id',$userid])
            ->orderBy('id desc');
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
            //echo $i;exit();
            
            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
           // $objPHPExcel->getActiveSheet()->setTitle(isset($user)?$user->name.'打卡记录':'人员打卡记录');
            $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')业务问题':'业务问题列表');
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
            ->setCellValue('A'.$i, '问题标题')
            ->setCellValue('B'.$i, '创建人')
            ->setCellValue('C'.$i, '协同部门')
            ->setCellValue('D'.$i, '部门')
            ->setCellValue('E'.$i, '优先级')
            ->setCellValue('F'.$i, '创建时间');
            //循环获取数据
            $i = 3;

            foreach ($model as $v) 
            {
                if($v['collaboration_department'] != "null")
                {
                    $department =  explode(",",$v['collaboration_department']);
                    for($j=0;$j<count($department);$j++)
                    {
                        $p[$j] = UserDepartment::find()->where(["id"=>$department[$j]])->one();
                        $deprt[] = $p[$j]['name'];
                    }
                    $depart_name = join("  ， ",$deprt);
                }
                else
                {
                    $depart_name =  $v['collaboration_department'];
                }
                $department_id = User::find()
                            ->select(["department_id"])
                            ->where(["id" => $v["user_id"]])
                            ->one();
                $department = UserDepartment::find()
                            ->select(["name"])
                            ->where(["id" => $department_id])
                            ->one();
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i,$v['problem_title'])
                ->setCellValue('B'.$i,$v['user_name'])
                ->setCellValue('C'.$i,$department["name"] )
                ->setCellValue('D'.$i,$v["department"])
                ->setCellValue('E'.$i,$v['priority'])
                ->setCellValue('F'.$i,date('Y-m-d H:m:s',$v['create_time']));
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
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
            $filename =isset($user)?$user->username.'('.$user->name.')业务问题':'业务问题列表';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();

        }
        //默认列表
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
        $searchModel = new problemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['in','user_id',$userid]);
//        $dataProvider->query->orderBy("id desc");//原按照提交问题的人员的ID降序排列
        $dataProvider->query->orderBy("create_time desc");//现在按照提交问题的时间降序排列
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single problem model.
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
     * Creates a new problem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new problem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->problem_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing problem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->problem_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing problem model.
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
     * Finds the problem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return problem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = problem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
