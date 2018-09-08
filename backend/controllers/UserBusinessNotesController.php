<?php

namespace backend\controllers;

use backend\models\UserBusiness;
use Yii;
use backend\models\UserBusinessNotes;
use backend\models\UserBusinessNotesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
use backend\models\User;
use backend\models\CompanyCategroy;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');


/**
 * UserBusinessNotesController implements the CRUD actions for UserBusinessNotes model.
 */
class UserBusinessNotesController extends Controller
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
     * Lists all UserBusinessNotes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $rank = Yii::$app->user->identity->rank;//人员职务级别
        if (Yii::$app->request->get('select'))//查询
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('UserBusinessNotesSearch');
            } else {
                $data = \Yii::$app->request->get('UserBusinessNotesSearch');
            }
            $username = $data['username'];
            $name = $data['name'];
            if (($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = $data['company_id'];
            }
            $department_id = empty($data['department']) ? "" : $data['department'];
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7);
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime();

            $searchModel = new UserBusinessNotesSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//            $dataProvider->query->orderBy('businessnotes.time desc');
            $dataProvider->query->andWhere(['between', 'businessnotes.time', $start_time, $end_time]);

            $companyid = Yii::$app->user->identity->company_categroy_id;
            if (empty($company_id))//没有选公司
            {
                if (!in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                    if ($rank == 30)//主公司经理
                    {
                        $child = CompanyCategroy::find()
                            ->select(["id"])
                            ->where(["fly" => $companyid])
                            ->asArray()
                            ->all();
                        $count = count($child);
                        if ($count > 0) {
                            foreach ($child as $k => $v) {
                                $company[$k] = $v['id'];
                                $company[$k + 1] = $companyid;
                            }
                        } else {
                            $company[0] = $companyid;
                        }
                        $where_company = ['in', "company_categroy_id", $company];
                    }
                    if ($rank == 3)//子公司经理
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
                    if (($rank == 4) && in_array("hr", $rules))//部门经理同时是hr
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    } elseif (($rank == 4) && !in_array("hr", $rules))//部门经理非hr
                    {
                        $where_company = ["department_id" => Yii::$app->user->identity->department_id];
                    }
                } else {
                    $where_company = "";
                }
            } else {
                $where_company = ["company_categroy_id" => $company_id];
            }
            $userid = User::find()
                ->select('username')
                ->where($where_company)
                ->asArray()
                ->column();
            $dataProvider->query->andWhere(['in', 'staff_num', $userid]);
            if (!empty($city)) {
                $dataProvider->query->andWhere(['user.domain_id' => $city]);
            }
            if (!empty($department_id) && $department_id != '请选择部门') {
                $dataProvider->query->andWhere(['department_id' => $department_id]);
            }
            if (!empty($username)) {
                if (is_numeric($username)) {
                    $user = User::findOne(['username' => $username]);
//                    var_dump($user->username);die;
                    if ($user) {
                        $dataProvider->query->andWhere(['staff_num' => $user->username]);
                    } else {
                        echo '<script>alert("用户不存在");history.back()</script>';
                    }
                } else {
                    echo '<script>alert("用户名不正确");history.back()</script>';
                }
            }
            if (!empty($name)) {

                $userdata = User::findOne(['name' => $name]);

                if ($userdata) {
                    $dataProvider->query->andWhere(['staff_num' => $userdata->username]);
                } else {
                    echo '<script>alert("用户不存在");history.back()</script>';
                }
            }
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            if ((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                $searchModel->area = $area;
                $searchModel->city = $city;
                $searchModel->company_id = $company_id;
            }
            $searchModel->department = $department_id;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        if (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('UserBusinessNotesSearch');
            } else {
                $data = \Yii::$app->request->get('UserBusinessNotesSearch');
            }
            $username = $data['username'];
            $name = $data['name'];
            if (($rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {
                $area = $data['area'];
                $city = $data['city'];
                $company_id = $data['company_id'];
            }
            $department_id = empty($data['department']) ? "" : $data['department'];
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7);
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime();

            $model = UserBusinessNotes::find()
                ->select('u.username,u.name,u.company_categroy_id,b.customer_name,off_user_business_notes.*')
                ->leftJoin(User::tableName() . ' u', UserBusinessNotes::tableName() . '.staff_num=u.username')
                ->leftJoin(UserBusiness::tableName() . ' b', UserBusinessNotes::tableName() . '.business_id=b.id');
            $companyid = Yii::$app->user->identity->company_categroy_id;
            if (empty($company_id))//没有选公司
            {
                if (!in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                    if ($rank == 30)//主公司经理
                    {
                        $child = CompanyCategroy::find()
                            ->select(["id"])
                            ->where(["fly" => $companyid])
                            ->asArray()
                            ->all();
                        $count = count($child);
                        if ($count > 0) {
                            foreach ($child as $k => $v) {
                                $company[$k] = $v['id'];
                                $company[$k + 1] = $companyid;
                            }
                        } else {
                            $company[0] = $companyid;
                        }
                        $where_company = ['in', "company_categroy_id", $company];
                    }
                    if ($rank == 3)//子公司经理
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    }
                    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
                    if (($rank == 4) && in_array("hr", $rules))//部门经理同时是hr
                    {
                        $where_company = ["company_categroy_id" => $companyid];
                    } elseif (($rank == 4) && !in_array("hr", $rules))//部门经理非hr
                    {
                        $where_company = ["department_id" => Yii::$app->user->identity->department_id];
                    }
                } else {
                    $where_company = "";
                }
            } else {
                $where_company = ["company_categroy_id" => $company_id];
            }
            $userid = User::find()
                ->select('username')
                ->where($where_company)
                ->asArray()
                ->column();
            if (!empty($city)) {
                $model->andWhere(['domain_id' => $city]);
            }
            if (!empty($department_id) && $department_id != '请选择部门') {
                $model->andWhere(['department_id' => $department_id]);
            }
            if (!empty($username)) {
                if (is_numeric($username)) {
                    $user = User::findOne(['username' => $username]);
                    if ($user) {
                        $model->andWhere(['staff_num' => $user->username]);
                    } else {
                        echo '<script>alert("用户不存在");history.back()</script>';
                    }
                } else {
                    echo '<script>alert("用户名不正确");history.back()</script>';
                }
            }
            if (!empty($name)) {
                $userdata = User::findOne(['name' => $name]);
                if ($userdata) {
                    $model->andWhere(['off_user_business_notes.staff_num' => $userdata->username]);
                } else {
                    echo '<script>alert("用户不存在");history.back()</script>';
                }
            }
            $model->andWhere(['between', 'off_user_business_notes.time', $start_time, $end_time])
                ->andWhere(['in', 'off_user_business_notes.staff_num', $userid])
                ->orderBy('off_user_business_notes.time desc');

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

            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
            // $objPHPExcel->getActiveSheet()->setTitle(isset($user)?$user->name.'打卡记录':'人员打卡记录');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, isset($user) ? $user->username . '(' . $user->name . ')跟进记录' : '跟进记录');
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            //列头
            $i = 2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '客户名称')
                ->setCellValue('B' . $i, '业务员工号')
                ->setCellValue('C' . $i, '姓名')
                ->setCellValue('D' . $i, '提交内容')
                ->setCellValue('E' . $i, '记录时间');
            //循环获取数据
            $i = 3;
            foreach ($model as $v) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $v['customer_name'])
                    ->setCellValue('B' . $i, $v['staff_num'])
                    ->setCellValue('C' . $i, $v['username'])
                    ->setCellValue('D' . $i, $v['followup_text'])
                    ->setCellValue('E' . $i, date('Y-m-d H:i:s', $v['time']));
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $i . ':E' . $i);
            $objPHPExcel->setActiveSheetIndex(0);


            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $filename = isset($user) ? $user->username . '(' . $user->name . ')跟进记录' : '跟进记录';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }

        $searchModel = new UserBusinessNotesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $companyid = Yii::$app->user->identity->company_categroy_id;
        $where_company = "";
        if (!in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
            if (Yii::$app->user->identity->rank == 30)//主公司经理
            {
                $child = CompanyCategroy::find()
                    ->select(["id"])
                    ->where(["fly" => $companyid])
                    ->asArray()
                    ->all();
                $count = count($child);
                if ($count > 0) {
                    foreach ($child as $k => $v) {
                        $company[$k] = $v['id'];
                        $company[$k + 1] = $companyid;
                    }
                } else {
                    $company[0] = $companyid;
                }
                $where_company = ['in', "company_categroy_id", $company];
            }
            if (Yii::$app->user->identity->rank == 3)//子公司或者部门经理
            {
                $where_company = ["company_categroy_id" => $companyid];
            }
            $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
            if ((Yii::$app->user->identity->rank == 4) && in_array("hr", $rules))//部门经理同时是hr
            {
                $where_company = ["company_categroy_id" => $companyid];
            } elseif ((Yii::$app->user->identity->rank == 4) && !in_array("hr", $rules))//部门经理非hr
            {
                $where_company = ["department_id" => Yii::$app->user->identity->department_id];
            }

        } else {
            $where_company = "";
        }
        $userid = User::find()
            ->select('username')
            ->where($where_company)
            ->asArray()
            ->column();
        $dataProvider->query->where(["in", "staff_num", $userid])
            ->orderBy("time desc");
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserBusinessNotes model.
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
     * Creates a new UserBusinessNotes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserBusinessNotes();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserBusinessNotes model.
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
     * Deletes an existing UserBusinessNotes model.
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
     * Finds the UserBusinessNotes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserBusinessNotes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserBusinessNotes::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /*
     * 添加是否作废操作（作废即记录不可见，取消作废即记录可见）
     * */
    public function actionChange($id)
    {
        $flage = $_GET['flage'];
        $data = UserBusinessNotes::findOne(['id' => $_GET['id']]);

        //作废操作
        if ($flage == 1) {
            $data->is_show = 2;
            if (!$data->save()) {
                echo '<script>alert("保存失败");history.back()</script>';
            }
        } //取消作废操作
        else {
            $data->is_show = '1';
            if (!$data->save()) {
                echo '<script>alert("保存失败");history.back()</script>';
            }
        }
        return $this->redirect(['index']);

    }

}
