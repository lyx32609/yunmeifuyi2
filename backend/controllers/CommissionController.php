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
use components\helpers\DateHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');


/**
 * CommissionController implements the CRUD actions for Orders model.
 */
class CommissionController extends Controller
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
            $check_status = $data['check_status'];      //审核状态 1为未审2已审 默认为未审
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
            //按照审核状态
            if (!empty($check_status)) {
//                var_dump(4545645);die;
                $dataProvider->query->andWhere(['check_status' => $check_status]);
            }

            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->department = $department_id;
            $searchModel->check_status = $check_status;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        } elseif (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('OrdersSearch');
            } else {
                $data = \Yii::$app->request->get('OrdersSearch');
            }

            $check_status = $data['check_status'];      //审核状态 1为未审2已审 默认为未审
            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $model = Orders::find()
                ->select('o.id,o.check_time,o.check_uid,o.order_id,o.staff_num,o.check_status,o.finishtime,o.payed,u.name as uname,d.name as dname')
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
            //按照审核状态
            if (!empty($check_status)) {
//                var_dump(4545645);die;
                $model->andWhere(['check_status' => $check_status]);
            }
            $data = $model->asArray()->all();
            //遍历每个订单的完成时间所属范围,并计算提成金额和提成比例
            foreach ($data as $key => $value) {
                $order_time = $value['finishtime'];
                $payed = $value['payed'].'元';
                $check_uid = $value['check_uid'];//审核人id
                $u_data = $this->getName($check_uid);
                $data[$key]['check_uname']=$u_data['name'];
                $data[$key]['check_staff_num']=$u_data['username'];
                $record_model = Record::find()
                    ->select('percent,start_time,end_time')
                    ->asArray()
                    ->all();
                $length = count($record_model);
                //先判断该订单是否大于off_record表中的最新时间，
                //如果大于就去off_percentum表中查找提成比例
                if ($order_time > $record_model[$length - 1]['end_time']) {

                    $percentum_data = Percentum::find()
                        ->select(['new_per'])
                        ->where(['is_open' => '1'])
                        ->asArray()
                        ->one();
                    $money = $payed * $percentum_data['new_per'] / 100;
                    $data[$key]['commission'] =$money . '元';
                    $data[$key]['per'] =$percentum_data['new_per']."%";
                }
                else {
                    $record_data = Record::find()
                        ->select(['id', 'start_time', 'end_time', 'percent'])
                        ->orderBy('end_time desc')
                        ->asArray()
                        ->all();
                    foreach ($record_data as $k => $v) {
                        if ($order_time >= $v['start_time'] && $order_time <= $v['end_time']) {
                            $money = $payed * $v['percent'] / 100;
                            $data[$key]['commission']= $money . '元';
                            $data[$key]['per']= $v['percent'] . "%";
                        }

                    }
                }
            }

            //遍历每个订单开始
//            $order_time = $model->finishtime;

            //遍历每个订单结束

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
                ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')提成审核':'提成审核');
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
                ->setCellValue('G'.$i, '审核时间')
                ->setCellValue('H'.$i, '审核状态')
                ->setCellValue('I'.$i, '审核人')
                ->setCellValue('J'.$i, '审核人账号');
            //循环获取数据
            $i = 3;

            foreach ($data as $v)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,$v['staff_num'])
                    ->setCellValue('B'.$i,$v['uname'])
                    ->setCellValue('C'.$i,$v['dname'])
                    ->setCellValue('D'.$i,$v['payed'].'元')
                    ->setCellValue('E'.$i,$v['per'])
                    ->setCellValue('F'.$i,$v['commission'])
                    ->setCellValue('G'.$i,$v['check_time']?date('Y-m-d H:i:s', $v['check_time']):'')
                    ->setCellValue('H'.$i,($v['check_status']==1)?'未审':'已审')
                    ->setCellValue('I'.$i,$v['check_uname'])
                    ->setCellValue('J'.$i,$v['check_staff_num'])
                ;
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':j'.$i);
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
            $filename =isset($user)?$user->username.'('.$user->name.'提成审核':'提成审核';
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

    private function getName($uid){
        $data = User::find()
            ->select(['username','name'])
            ->where(['id'=>$uid])
            ->asArray()
            ->one();
        return $data;

    }


//$searchModel = new OrdersSearch();
//$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//return $this->render('index', ['searchModel' => $searchModel,
//'dataProvider' => $dataProvider,]);
//}

    /*
     * 财务审核（批量，单个）*/
    public function actionCheck()
    {
        $uid = \Yii::$app->user->id;    //获取当前登录人id
        //选择审核的所有id(批量选择)
        if (isset($_GET['ids'])) {
            $ids = explode(',', $_GET['ids']);
            foreach ($ids as $k => $v) {
//                $a[$k] = $this->orderStatus($v);
                //获取该订单的状态 1为未审核 2为已审核
                if ($this->orderStatus($v) == 1) {
                    $data = Orders::findOne(['id' => $v]);
                    if ($data) {
                        $data->check_status = '2';
                        $data->check_time = time();
                        $data->check_uid = $uid;
                        if (!$data->save()) {
                            $this->setError('审核失败');
                            return false;
                        }
                    }
                }
            }
            return true;
        } else {
            $data = Orders::findOne(['id' => $_GET['id']]);
            $data->check_status = '2';
            $data->check_time = time();
            $data->check_uid = $uid;
            if (!$data->save()) {
                $this->setError('审核失败');
                return false;
            } else {
                $searchModel = new OrdersSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }

        }
    }

    /*
     * 获取该订单的状态*/
    private function orderStatus($id)
    {
        $data = Orders::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();
        return $data['check_status'];

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

    /**
     * 按照登录人的角色等级获取部门名称和ID
     * @return string
     *
     */
    public function actionGetDepartment()
    {
        $user_id = $_GET['user_id'];
        $department = User::find()
            ->select('u.id,u.department_id,d.id,d.name as name')
            ->from(User::tableName() . ' As u')
            ->leftjoin(UserDepartment::tableName() . ' As d', 'u.company_categroy_id =d.company')
            ->where(['u.id' => $user_id])
            ->asArray()
            ->all();
//        var_dump($department);die;
        $data = '';
        if (count($department) > 0) {
            if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                $data = "<option>请选择部门</option>";
                foreach ($department as $v) {
                    $select = '';
                    $data .= "<option value='" . $v['id'] . "' $select  >" . $v['name'] . "</option>";
                }
            } else {
                foreach ($department as $v) {
                    $select = '';
                    $data .= "<option value='" . $v['id'] . "' $select  >" . $v['name'] . "</option>";
                }
            }

        } else {
            $data .= "<option>暂无部门</option>";
        }
        return $data;
    }


}
