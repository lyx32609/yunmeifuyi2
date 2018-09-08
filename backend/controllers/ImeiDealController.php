<?php

namespace backend\controllers;

use Yii;
use backend\models\PutImei;
use backend\models\User;
use backend\models\PutImeiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;

require(__DIR__ . '/../../vendor/excel/PHPExcel.php');

/**
 * ImeiDealController implements the CRUD actions for PutImei model.
 */
class ImeiDealController extends Controller
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
     * Lists all PutImei models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('select'))//查询
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('PutImeiSearch');
            } else {
                $data = \Yii::$app->request->get('PutImeiSearch');
            }
            $times = $data['times'];
            $area = $data['area'];
            $city = $data['city'];
            $company_id = $data['company_categroy_id'];
            $department_id = empty($data['department']) ? "" : $data['department'];
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7);
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime();
            $searchModel = new PutImeiSearch();

            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            $searchModel->area = $area;
            $searchModel->city = $city;
            $searchModel->department = $department_id;
            $searchModel->company_categroy_id = $company_id;
            $searchModel->times = $times;

            return $this->render('index', [
                'searchModel' => $searchModel,
            ]);
        } elseif (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('PutImeiSearch');
            } else {
                $data = \Yii::$app->request->get('PutImeiSearch');
            }

            $times = $data['times'];
            $area = $data['area'];
            $city = $data['city'];
            $username = $data['username'];
            $name = $data['name'];

            $company_id = $data['company_categroy_id'];
            $department_id = empty($data['department']) ? "" : $data['department'];
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7);
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime();
            $searchModel = new PutImeiSearch();


            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            $searchModel->area = $area;
            $searchModel->city = $city;
            $searchModel->department = $department_id;
            $searchModel->company_categroy_id = $company_id;
            $searchModel->times = $times;
            if ($department_id == '暂无部门' && $times == '请选择更换次数' && $name == '' && $username == '') {
                $sql = 'SELECT count(*) AS num,	p.id,	p.`user_id`,	p.`new_brand`,	p.`submit_time`,	u.`name`,	d.`name` department_name,	
            c.`name` company_name FROM(SELECT*	FROM off_put_imei	WHERE`status` = 1	ORDER BY id DESC	) AS p 
            LEFT JOIN off_user u ON p.user_id = u.id LEFT JOIN off_user_department d ON d.id = p.department_id
            LEFT JOIN off_company_categroy c ON c.id = p.company_categroy_id GROUP BY	p.user_id	ORDER BY p.id DESC';
                $result = PutImei::findBySql($sql)
                    ->asArray()
                    ->all();
            } else {
                $sql = 'SELECT count(*) AS num,p.id,p.`user_id`,p.`new_brand`,p.`submit_time`,u.`name`,d.`name` department_name,c.`name`company_name FROM';
                $sql .= '(SELECT*	FROM off_put_imei	';

                if (!empty($start_time) && !empty($end_time)) {
                    $sql .= " where submit_time between " . "$start_time " . "and " . $end_time;
                }
                $sql .= ' and `status` = 1 ';
                if (!empty($company_id) && $company_id != '请选择公司') {
                    $sql .= " AND company_categroy_id = " . "$company_id ";
                }
                if (!empty($department_id) && $department_id != '请选择部门' && $department_id != '暂无部门') {
                    $sql .= " AND department_id = " . "$department_id ";
                }
                //根据账号查询
                if (!empty($username)) {
                    //查询用户ID
                    $user_data = User::find()
                        ->select(['id'])
                        ->where(['username' => $username])
                        ->asArray()
                        ->one();
                    if ($user_data) {
                        $user_id = $user_data['id'];
                        $sql .= " AND user_id = " . "$user_id";
                    } else {
                        echo "<script>alert('输入的账号不存在，请输入正确账号');</script>";
                        return $this->render('index', [
                            'searchModel' => $searchModel,
                        ]);
                    }

                }
                //根据用户名查询
                if (!empty($name)) {
                    //查询用户ID
                    $user_data = User::find()
                        ->select(['id'])
                        ->where(['name' => $name])
                        ->asArray()
                        ->one();
                    if ($user_data) {
                        $user_id = $user_data['id'];
                        $sql .= " AND user_id = " . "$user_id";
                    } else {
                        echo "<script>alert('输入的用户名不存在，请输入正确的用户名');</script>";
                        return $this->render('index', [
                            'searchModel' => $searchModel,
                        ]);
                    }

                }
                $sql .= ' ORDER BY id DESC) AS p 
                    LEFT JOIN off_user u ON p.user_id = u.id 
                    LEFT JOIN off_user_department d ON d.id = p.department_id
                    LEFT JOIN off_company_categroy c ON c.id = p.company_categroy_id 
                    GROUP BY p.user_id	';
                if (!empty($times) && $times != '请选择更换次数') {
                    $sql .= "having num = " . $times;
                }
                $sql .= ' ORDER BY p.id DESC';
                $result = PutImei::findBySql($sql)
                    ->asArray()
                    ->all();
                if (!$result) {
                    echo "<script>alert('暂无数据');</script>";
                    return $this->render('index', [
                        'searchModel' => $searchModel,
                    ]);
                }

            }


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

            $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, isset($user) ? $user->username . '(' . $user->name . ')设备列表' : '设备待审列表记录');
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
                ->setCellValue('A' . $i, '姓名')
                ->setCellValue('B' . $i, '所属公司')
                ->setCellValue('C' . $i, '所属部门')
                ->setCellValue('D' . $i, '设备信息')
                ->setCellValue('E' . $i, '待审条数')
                ->setCellValue('F' . $i, '用户设备提交时间');
            //循环获取数据
            $i = 3;

            foreach ($result as $v) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $v['name'])
                    ->setCellValue('B' . $i, $v['company_name'])
                    ->setCellValue('C' . $i, $v['department_name'])
                    ->setCellValue('D' . $i, $v['new_brand'])
                    ->setCellValue('E' . $i, $v['num'])
                    ->setCellValue('F' . $i, date('Y-m-d H:i:s', $v['submit_time']));
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $i . ':D' . $i);
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
            $filename = isset($user) ? $user->username . '(' . $user->name . ')设备待审列表记录' : '设备待审列表记录';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit();
        }
        $searchModel = new PutImeiSearch();


        return $this->render('index', [
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single PutImei model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PutImei model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PutImei();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PutImei model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing PutImei model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PutImei model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PutImei the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PutImei::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * 待审核状态的设备列表
     */
    public function actionDeal($id)
    {
        $model = PutImei::findOne($id);
        $model->status = '2';
        $model->is_read = '2';
        $model->pass_time = time();
        if ($model->save()) {
            $phone = PutImei::find()
                ->select(['user_id', 'new_imei_number', 'new_brand', 'pass_time'])
                ->where(['id' => $id])
                ->asArray()
                ->one();
            $user = User::findOne($phone['user_id']);
            $user->phone_imei = $phone['new_imei_number'];
            $user->imei_time = $phone['pass_time'];
            $user->phone_brand = $phone['new_brand'];
            if ($user->save()) {
                echo "<script language=\"javascript\">alert(\"已审核通过，可去设备变更记录查看哦！\");top.location='index';</script>";
            } else {
                echo "<script>alert('审核失败');</script>";

                $searchModel = new PutImeiSearch();
                return $this->render('index', [
                    'searchModel' => $searchModel,
                ]);
            }

        } else {
            return '审核失败';
        }

    }

    /**
     * 获取审核通过的设备记录数
     */
    public function actionGetStatus()
    {
        $num = $_GET['num'];
//        $company_id = $_GET['company_id'];
        $model = PutImei::find()
            ->where(['status' => 1])
//            ->andWhere(['company_categroy_id'=>$company_id])
            ->asArray()
            ->all();
        $count = count($model);
        ($count > $num) ? $data['flage'] = '1' : $data['flage'] = '2';
        $data = json_encode($data);
        return $data;
    }

}
