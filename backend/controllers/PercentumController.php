<?php

namespace backend\controllers;

use backend\models\Orders;
use backend\models\Record;
use backend\models\TimeRecord;
use backend\models\User;
use backend\models\UserDepartment;
use Yii;
use backend\models\Percentum;
use backend\models\PercentumSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');
/**
 * PercentumController implements the CRUD actions for Percentum model.
 */
class PercentumController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    /**
     * Lists all Percentum models.
     * @return mixed
     */
    public function actionIndex()
    {
        //查询
        if (Yii::$app->request->get('select') == 'select'){
            $searchModel = new PercentumSearch();
            $params = Yii::$app->request->queryParams;
            $data = \Yii::$app->request->get('PercentumSearch');

            $data['start_time'] = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7);
            $data['end_time'] = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime();

            $username = $data["username"];   //账号
            $name = $data["name"];   //姓名
            $department_id = $data["department_id"];

            //保留查询条件
            $searchModel->start_time = $data['start_time'];
            $searchModel->end_time = $data['end_time'];
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->department_id = $department_id;
            $params['PercentumSearch'] = $data;

            $dataProvider = $searchModel->search($params);
            $new_per = Percentum::find()->where(['flag'=>1])->one();
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'new_per'     =>$new_per
            ]);
        }
        elseif (Yii::$app->request->get('open') =='1')
        {
            $type = Percentum::find()
                ->where(['flag'=>1])
                ->one();
            $type->is_open = 1;
            $type->open_time = time();
            $type->close_time = '';
            if ($type->save()){
                return json_encode(['msg'=>'1']);
            }else{
                return json_encode(['msg'=>'2']);
            }
        }
        elseif (Yii::$app->request->get('close') =='2')
        {
            $type = Percentum::find()
                ->where(['flag'=>1])
                ->one();
            $type->is_open = 2;
            $type->close_time = time();
            if ($type->save()){
                  return json_encode(['msg'=>'1']);
            }else{
                return json_encode(['msg'=>'2']);
            }
        }
        elseif (Yii::$app->request->get('export') == 'export')
        {  //导出
            $searchModel = new PercentumSearch();
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('PercentumSearch');
            } else {
                $data = \Yii::$app->request->get('PercentumSearch');
            }
//            var_dump($data);die;
            $starttime = !empty($data['start_time']) ? strtotime($data['start_time']) : '';
            $endtime = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : '';

            $username = $data["username"];   //账号
            $name = $data["name"];   //姓名
            $department_id = $data["department_id"];

            //保留查询条件
            $searchModel->start_time = $data['start_time'];
            $searchModel->end_time = $data['end_time'];
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->department_id = $department_id;

            $model = Percentum::find();
            if (!empty($starttime) || !empty($endtime)){
                $model->andFilterWhere(['between', 'time', $starttime, $endtime]);
            }
            if (!empty($username)){
                $model->andFilterWhere(['like', 'username', $username]);
            }
            if (!empty($name)){
                $model->andFilterWhere(['like', 'name', $name]);
            }
            if (!empty($department_id)){
                $model->andFilterWhere(['department_id'=>$department_id]);
            }
            $model = $model
                ->andFilterWhere(['flag'=>0])
                ->orderBy('time desc')
                ->asArray()
                ->all();
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
            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '提成修改记录');
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
                ->setCellValue('A' . $i, '账号')
                ->setCellValue('B' . $i, '姓名')
                ->setCellValue('C' . $i, '部门')
                ->setCellValue('D' . $i, '操作内容')
                ->setCellValue('E' . $i, '修改前比例')
                ->setCellValue('F' . $i, '修改后比例')
                ->setCellValue('G' . $i, '修改时间')
            ;
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i . ':L' . $i)->applyFromArray(
                array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                )
            );
            //循环获取数据
            $i = 3;
            foreach ($model as $v) {
               $department =  UserDepartment::find()
                   ->where(['id'=>$v['department_id']])
                   ->asArray()
                   ->one();

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $v['username'])
                    ->setCellValue('B' . $i, $v['name'])
                    ->setCellValue('C' . $i, $department['name'])
                    ->setCellValue('D' . $i, $v['content'])
                    ->setCellValue('E' . $i, $v['old_per'] . '%')
                    ->setCellValue('F' . $i, $v['new_per'] . '%')
                    ->setCellValue('G' . $i, date('Y-m-d H:i:s', $v['time']));
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
                $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
                $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A' . $i . ':G' . $i)->applyFromArray(
                    array(
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                        )
                    )
                );
                $i++;
            }
            $objPHPExcel->setActiveSheetIndex(0);
            $filename = '提成修改记录';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();
        }
        
        $searchModel = new PercentumSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $new_per = Percentum::find()->where(['flag'=>1])->one();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'new_per'=>$new_per,
        ]);
    }

    /**
     * Displays a single Percentum model.
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
     * Creates a new Percentum model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

    }

    /**
     * Updates an existing Percentum model.
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
     * Deletes an existing Percentum model.
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
     * Finds the Percentum model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Percentum the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Percentum::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionDeal()
    {
        if (!empty($_GET['new_per'])){
            $new_per = $_GET['new_per'];
            if (!is_numeric($new_per)){
                echo "<script>alert('请输入数字！');history.back()</script>";
                return false;
            }
            $res = Percentum::find()->asArray()->all();
            // 如果表中没有数据 第一次 写入的默认是 当前使用的 百分比折扣
            if (empty($res)){
                $model = new Percentum();
                $model->username = Yii::$app->user->identity->username;
                $model->name = Yii::$app->user->identity->name;
                $model->new_per = $new_per;
                $model->time = time();
                $model->flag = 1;    //当前使用的标志
                $model->content = '修改提成比例';
                $model->is_open = 2;
                $model->department_id = Yii::$app->user->identity->department_id;
                if ($model->save()){
                    return $this->redirect(['index']);
                }else {
                    return $this->redirect(['index']);
                }
            }else{
                //首先先添加历史记录  从 flag =1 的记录中把 原来的数据放在历史的记录中
                $model1 = new Percentum();
                $old_result = Percentum::find()->where(['flag'=>1])->one();
                if ($old_result->is_open != 1){
                    echo "<script>alert('请先开启！');history.back()</script>";
                    return false;
                }
                $time = time();
                $record = new Record();
                $last = Percentum::find()
                    ->orderBy('id desc')
                    ->one();
                $record->start_time = $last->open_time;
                $record->end_time = $time;
                $record->percent = $last->new_per;
                if ($record->save()){
                    $model1->username = Yii::$app->user->identity->username;
                    $model1->name = Yii::$app->user->identity->name;
                    $model1->new_per = $new_per;
                    $model1->time = $time;
                    $model1->open_time = $time;
                    $model1->old_per = $old_result->new_per;
                    $model1->flag = 0;
                    $model1->content = '修改提成比例';
                    $model1->department_id = Yii::$app->user->identity->department_id;
                    if ($model1->save()){
                        $old_result->new_per = $new_per;
                        $old_result->time = $time;
                        $old_result->username = Yii::$app->user->identity->username;
                        $old_result->name = Yii::$app->user->identity->name;
                        //然后更新当前正在使用的百分比记录
                        if ($old_result->save()){
                            return $this->redirect(['index']);
                        } else {
                            return $this->redirect(['index']);
                        }
                    }
                }
            }
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * 修改订单确认时间
     * @return \yii\web\Response
     */
    public function actionDayDeal()
    {
        if ($_GET['day'] != null){
            $day = $_GET['day'];
            $percent = Percentum::find()
                ->where(['flag'=>1])
                ->one();
            $percent->confirm_day = $day;
            if ($percent->save())
            {
                return $this->redirect(['index']);
            }else{
                return $this->redirect(['index']);
            }
        }
    }
    /**
     * 定时获取数据
     * @return bool
     */
    public function actionOrders()
    {
        set_time_limit(0);
        $record = Orders::find()->asArray()->all();
        $record2 = Record::find()->asArray()->all();
        //是否设置 提成比例
        $is_open = Percentum::find()
            ->where(['flag'=>1])
            ->one();
        if (empty($is_open)){
            return false;
        }
        // 如果开启了 提成比例
        if ($is_open->is_open == 1){
            // 如果表中之前没有数据 获取的是从开启的时间到 当前时间的数据
            if (empty($record)){
                $start_time = $is_open->open_time;
                $end_time = time()-(60*60*24*$is_open->confirm_day);
                $time = new TimeRecord();
                $time->time = $end_time;
                if (!$time->save()){
                    return false;
                }
                //如果表中之前有数据 获取的是从当前表中最后的时间到 当前时间的数据
            }else{
                $last = TimeRecord::find()
                    ->orderBy('id desc')
                    ->asArray()
                    ->one();
                $start_time = $last['time'] + 1;
                $end_time = time()-(60*60*24*$is_open->confirm_day);
                $time = new TimeRecord();
                $time->time = $end_time;
                if (!$time->save()){
                    return false;
                }
            }
            //未开启 提成比例
        }else{
            $time = new TimeRecord();
            $time->time = time()-(60*60*24*$is_open->confirm_day);
            if (!$time->save()){
                return false;
            }
            return true;
        }
        // 查询需要 请求的人员
        $user = User::find()
            ->select('username')
            ->where(['department_id'=>579])
            ->andFilterWhere(['is_staff'=>'1'])
            ->asArray()
            ->all();
//        var_dump($user);die;
        // 遍历请求接口  写入数据库中
        foreach ($user as $key=> $va)
        {
            $res = $this->countSum($va['username'], $start_time, $end_time);
            if (!empty($res)){
                foreach ($res as $value)
                {
                    if (!empty($record2)){
                        foreach ($record2 as $val)
                        {
                            if ($value['finishtime'] >= $val['start_time'] && $value['finishtime'] <= $val['end_time'])
                            {
                                $aa =  $val['percent'];
                            }elseif($value['finishtime'] > $is_open->time){
                                $aa =  $is_open->new_per;
                            }
                        }
                    }else{
                        $aa =  $is_open->new_per;
                    }
                    $orders = new Orders();
                    $orders->order_id = $value['order_id'];
                    $orders->staff_num = $va['username'];
                    $orders->createtime = $value['createtime'];
                    $orders->finishtime = $value['finishtime'];
                    $orders->company_id = $value['company_id'];
                    $orders->payed = $value['payed'];
                    $orders->status = $value['status'];
                    $orders->company_name = $value['shopname'];
                    $orders->uname = $value['uname'];
                    $orders->money = round($value['payed'] * ($aa/100),2);
                    $orders->percent = $aa;
                    $a = $orders->save();
                }
            }else{
                $a = [];
            }
        }
        if ($a){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 接口请求
     * @param $username  用户名
     * @param $start_time  开始时间
     * @param $endtime   结束时间
     * @return array  返回的数据
     */
    public function countSum($username, $start_time, $endtime)
    {
        $data['staff'] = $username;
        $data['start_time'] = $start_time;
        $data['end_time'] = $endtime;
        // 调集采接口 获取员工订单
        $result = \Yii::$app->api->request('order/staffOrders', $data);
        if ($result['ret'] != 0) {
            return [];
        }
        $order = $result['data'];

        return $order;
    }
}
