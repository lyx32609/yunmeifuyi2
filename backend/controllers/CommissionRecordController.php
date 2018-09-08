<?php

namespace backend\controllers;

use Yii;
use backend\models\Orders;
use backend\models\OrdersSearch;
use backend\models\UserDepartment;
use backend\models\Percentum;
use backend\models\Record;
use backend\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');


/**
 * CommissionRecordController implements the CRUD actions for Orders model.
 */
class CommissionRecordController extends Controller
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
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex()

    {
        if (Yii::$app->request->get('select'))//查询
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('OrdersSearch');
            } else {
                $data = \Yii::$app->request->get('OrdersSearch');
            }
            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $searchModel = new OrdersSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->Where(['between', 'finishtime', $start_time, $end_time]);
            $dataProvider->query->orderBy('finishtime desc');

            //如果账号不为空
            if (!empty($username)) {
                $dataProvider->query->andWhere(['staff_num' => $username]);
            }
            //如果姓名不为空
            if (!empty($name)) {

                $u_data = User::find()
                    ->select('username')
                    ->Where(['name' => $name])
                    ->asArray()
                    ->one();
                $dataProvider->query->andWhere(['staff_num' => $u_data['username']]);
            }
            //按照部门查找
            if (!empty($department_id)) {
                if (empty($name) && empty($username)) {
                    $user_data = User::find()
                        ->select(['username'])
                        ->where(['department_id' => $department_id])
                        ->asArray()
                        ->all();
                    if ($user_data) {
                        foreach ($user_data as $k => $v) {
                            $arr[] = $v['username'];//该部门下的所有账号
                        }
                        $dataProvider->query->andWhere(['in', 'staff_num', $arr]);
                    }
                }
            }

            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->department = $department_id;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        }
        elseif (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('OrdersSearch');
            } else {
                $data = \Yii::$app->request->get('OrdersSearch');
            }

            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $model = Orders::find()
                ->select('o.id,o.check_time,o.check_uid,o.order_id,o.staff_num,o.check_status,o.finishtime,o.payed,o.money,o.percent,u.name as uname,d.name as dname')
                ->from(Orders::tableName() . ' as o')
                ->leftJoin(User::tableName() . ' as u', 'o.staff_num=u.username')
                ->leftJoin(UserDepartment::tableName() . ' as d', 'd.id = u.department_id')
                ->orderBy('o.finishtime desc')
                ->where(['between', 'o.finishtime', $start_time, $end_time]);
            //如果账号不为空
            if (!empty($username)) {
                $model->andWhere(['o.staff_num' => $username]);

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
            //按照部门查找
            if (!empty($department_id)) {
                if (empty($name) && empty($username)) {
                    $user_data = User::find()
                        ->select(['username'])
                        ->where(['department_id' => $department_id])
                        ->asArray()
                        ->all();
                    if ($user_data) {
                        foreach ($user_data as $k => $v) {
                            $arr[] = $v['username'];//该部门下的所有账号
                        }
                        $model->andWhere(['in', 'staff_num', $arr]);
                    }
                }
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

            $objPHPExcel->getActiveSheet()->mergeCells('A1:j1');
            // $objPHPExcel->getActiveSheet()->setTitle(isset($user)?$user->name.'打卡记录':'人员打卡记录');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')提成记录':'提成记录');
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
                ->setCellValue('D'.$i, '订单金额')
                ->setCellValue('E'.$i, '提成比例')
                ->setCellValue('F'.$i, '提成金额')
                ->setCellValue('G'.$i, '订单完成时间');
            //循环获取数据
            $i = 3;

            foreach ($data as $v)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,$v['staff_num'])
                    ->setCellValue('B'.$i,$v['uname'])
                    ->setCellValue('C'.$i,$v['dname'])
                    ->setCellValue('D'.$i,round($v['payed'],2).'元')
                    ->setCellValue('E'.$i,$v['percent']."%")
                    ->setCellValue('F'.$i,round($v['money'],2).'元')
                    ->setCellValue('G'.$i,$v['finishtime']?date('Y-m-d H:i:s', $v['finishtime']):'')
                ;
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':G'.$i);
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
            $filename =isset($user)?$user->username.'('.$user->name.'提成记录':'提成记录';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();

        }



        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Orders model.
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
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Orders();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Orders model.
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
     * Deletes an existing Orders model.
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
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function getName($uid){
        $data = User::find()
            ->select(['username','name'])
            ->where(['id'=>$uid])
            ->asArray()
            ->one();
        return $data;

    }
}
