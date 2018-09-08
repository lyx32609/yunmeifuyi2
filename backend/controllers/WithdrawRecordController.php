<?php

namespace backend\controllers;

use Yii;
use backend\models\WithdrawRecord;
use backend\models\WithdrawRecordSearch;
use backend\models\User;
use backend\models\UserDepartment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');

/**
 * WithdrawRecordController implements the CRUD actions for WithdrawRecord model.
 */
class WithdrawRecordController extends Controller
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
     * Lists all WithdrawRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('select'))//查询
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('WithdrawRecordSearch');
            } else {
                $data = \Yii::$app->request->get('WithdrawRecordSearch');
            }
//            var_dump(1111);die;
            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $searchModel = new WithdrawRecordSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->Where(['between', 'time', $start_time, $end_time]);
            $dataProvider->query->andWhere(['flag'=>'2']);
            $dataProvider->query->orderBy('time desc');

            //如果账号不为空
            if (!empty($username)) {
                $dataProvider->query->andWhere(['staff_num' => $username]);            }

            //如果姓名不为空
            if (!empty($name)) {

                $u_data = User::find()
                    ->select('username')
                    ->Where(['name' => $name])
                    ->asArray()
                    ->one();
                $dataProvider->query->andWhere(['staff_num' => $u_data['username']]);
            }
            //判断当前登录账号是否为超管
            if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                $staff_num = "";//可以看到全部的人员
            }
            else {
                $company = Yii::$app->user->identity->company_categroy_id;
                if (!$company) {
                    echo "<script>alert('当前登录账号公司ID不存在！');history.back()</script>";
                    return false;
                }
                $user_data = User::find()
                    ->select('username')
                    ->where(['company_categroy_id' => $company])
                    ->asArray()
                    ->all();
                $staff_nums = array_column($user_data, 'username');
//            var_dump($staff_nums);die;
                $staff_num = ["in", "staff_num", $staff_nums];
            }
            $dataProvider->query->andWhere($staff_num);
            //按照部门查找
            if (!empty($department_id)) {
                $u_data = User::find()
                    ->select('username')
                    ->Where(['department_id' => $department_id])
                    ->asArray()
                    ->all();
                $staff_nums = array_column($u_data,'username');
//                var_dump($staff_nums);die;
                $dataProvider->query->andWhere(["in", "staff_num", $staff_nums]);
            }

            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            $searchModel->username = $username;                 //账号
            $searchModel->name = $name;                         //姓名
            $searchModel->department = $department_id;          //部门id
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        }
        elseif (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('WithdrawRecordSearch');
            } else {
                $data = \Yii::$app->request->get('WithdrawRecordSearch');
            }

            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $model = WithdrawRecord::find()
                ->select('w.time,w.staff_num,w.money,u.name,d.name as dname')
                ->from(WithdrawRecord::tableName() . ' as w')
                ->leftJoin(User::tableName() . ' as u', 'w.staff_num=u.username')
                ->leftJoin(UserDepartment::tableName() . ' as d', 'd.id = u.department_id')
                ->where(['between', 'w.time', $start_time, $end_time])
                ->andwhere(['w.flag'=>'2'])
                ->orderBy('w.time desc')
            ;
            //如果账号不为空
            if (!empty($username)) {
                $model->andWhere(['w.staff_num' => $username]);
            }
            //如果姓名不为空
            if (!empty($name)) {

                $u_data = User::find()
                    ->select('username')
                    ->Where(['name' => $name])
                    ->asArray()
                    ->one();
                $model->andWhere(['staff_num' => $u_data['username']]);
            }
            //判断当前登录账号是否为超管
            if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                $staff_num = "";//可以看到全部的人员
            }
            else {
                $company = Yii::$app->user->identity->company_categroy_id;
                if (!$company) {
                    echo "<script>alert('当前登录账号公司ID不存在！');history.back()</script>";
                    return false;
                }
                $user_data = User::find()
                    ->select('username')
                    ->where(['company_categroy_id' => $company])
                    ->asArray()
                    ->all();
                $staff_nums = array_column($user_data, 'username');
                $staff_num = ["in", "staff_num", $staff_nums];
            }
            $model->andWhere($staff_num);

            //按照部门查找
            if (!empty($department_id)) {
                $u_data = User::find()
                    ->select('username')
                    ->Where(['department_id' => $department_id])
                    ->asArray()
                    ->all();
                $staff_nums = array_column($u_data,'username');
                $model->andWhere(["in", "staff_num", $staff_nums]);
            }
            $data = $model->asArray()->all();

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
                ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')提现记录':'提现记录');
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
                ->setCellValue('A'.$i, '账号')
                ->setCellValue('B'.$i, '姓名')
                ->setCellValue('C'.$i, '部门')
                ->setCellValue('D'.$i, '金额')
                ->setCellValue('E'.$i, '时间');
            //循环获取数据
            $i = 3;

            foreach ($data as $v)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,$v['staff_num'])
                    ->setCellValue('B'.$i,$v['name'])
                    ->setCellValue('C'.$i,$v['dname'])
                    ->setCellValue('D'.$i,$v['money'].'元')
                    ->setCellValue('E'.$i,$v['time']?date('Y-m-d H:i:s', $v['time']):'')
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
            $filename =isset($user)?$user->username.'('.$user->name.'提现记录':'提现记录';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();

        }


        $searchModel = new WithdrawRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WithdrawRecord model.
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
     * Creates a new WithdrawRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WithdrawRecord();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WithdrawRecord model.
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
     * Deletes an existing WithdrawRecord model.
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
     * Finds the WithdrawRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WithdrawRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WithdrawRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
