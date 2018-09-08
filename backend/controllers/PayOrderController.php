<?php

namespace backend\controllers;

use Yii;
use backend\models\Orders;
use backend\models\OrdersSearch;
use backend\models\UserDepartment;
use backend\models\Percentum;
use backend\models\Record;
use backend\models\User;
use backend\models\OrdersSum;
use yii\web\Controller;
use components\helpers\DateHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\foundation\JPush\Client;
use backend\models\JpushLog;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');
use components\BaseController;

/**
 * PayOrderController implements the CRUD actions for Orders model.
 */
class PayOrderController extends BaseController
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
            $pay_status = $data['pay_status'];      //支付状态 1未支付2已支付 默认未支付
            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $searchModel = new OrdersSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $non_data = Orders::find()->select(["sum(money) as num"]);
            $paed_data = Orders::find()->select(["sum(money) as num"]);
            $dataProvider->query->Where(['between', 'finishtime', $start_time, $end_time]);
            $non_data->Where(['between', 'finishtime', $start_time, $end_time]);
            $paed_data->Where(['between', 'finishtime', $start_time, $end_time]);
            $dataProvider->query->orderBy('finishtime desc');
            //如果账号不为空
            if (!empty($username)) {
                $dataProvider->query->andWhere(['staff_num' => $username]);
                $non_data->andWhere(['staff_num' => $username]);
                $paed_data->andWhere(['staff_num' => $username]);
            }
            //如果姓名不为空
            if (!empty($name))
            {
                $data = User::find()->select(["username"])->where(['name' => $name])->asArray()->all();
                if($data)
                {
                    foreach ($data as $key => $value)
                    {
                        $u_data[] = $value['username'];
                    }
                    $dataProvider->query->andWhere(['in','staff_num',$u_data]);
                    $non_data->andWhere(['in','staff_num',$u_data]);
                    $paed_data->andWhere(['in','staff_num',$u_data]);
                }

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
                        $non_data->andWhere(['in', 'staff_num', $arr]);
                        $paed_data->andWhere(['in', 'staff_num', $arr]);
                    }
                }
            }
            //按照支付状态
            if (!empty($pay_status)) {
                $dataProvider->query->andWhere(['pay_status' => $pay_status]);
                $non_data->andWhere(['pay_status' => $pay_status]);
                $paed_data->andWhere(['pay_status' => $pay_status]);
            }
            
            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->department = $department_id;
            $searchModel->pay_status = $pay_status;
            $non = $non_data->andWhere(["pay_status"=>1])->asArray()->one();
            $paed = $paed_data->andWhere(["pay_status"=>2])->asArray()->one();



            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'non'=>$non,
                'paed'=>$paed
            ]);

        } elseif (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('OrdersSearch');
            } else {
                $data = \Yii::$app->request->get('OrdersSearch');
            }

            $pay_status = $data['pay_status'];      //支付状态 1未支付 2已支付
            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $model = Orders::find()
                ->select('o.id,o.check_time,o.check_uid,o.order_id,o.staff_num,o.pay_status,o.finishtime,o.pay_time,o.payed,o.pay_uid,o.pay_status,o.money,o.percent,u.name as uname,d.name as dname')
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
                $data = User::find()->select(["username"])->where(['name' => $name])->asArray()->all();
                if($data)
                {
                    foreach ($data as $key => $value)
                    {
                        $u_data[] = $value['username'];
                    }
                    $model->andWhere(['in','staff_num' , $u_data]);
                }
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
            //按照支付状态
            if (!empty($pay_status)) {
                $model->andWhere(['pay_status' => $pay_status]);
            }
            $data = $model->asArray()->all();
            //遍历每个订单的完成时间所属范围,并计算提成金额和提成比例
            foreach ($data as $key => $value)
            {
                $pay_uid = $value['pay_uid'];//支付人id
                $u_data = $this->getName($pay_uid);
                $data[$key]['pay_uname']=$u_data['name'];
                $data[$key]['pay_staff_num']=$u_data['username'];
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

            $objPHPExcel->getActiveSheet()->mergeCells('A1:j1');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')提成支付':'提成支付');
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
                ->setCellValue('G'.$i, '完成时间')
                ->setCellValue('H'.$i, '支付时间')
                ->setCellValue('I'.$i, '支付状态')
                ->setCellValue('J'.$i, '支付人')
                ->setCellValue('K'.$i, '支付人账号');
            //循环获取数据
            $i = 3;

            foreach ($data as $v)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,$v['staff_num'])
                    ->setCellValue('B'.$i,$v['uname'])
                    ->setCellValue('C'.$i,$v['dname'])
                    ->setCellValue('D'.$i,round($v['payed'],2).'元')
                    ->setCellValue('E'.$i,$v['percent'].'%')
                    ->setCellValue('F'.$i,$v['money'].'元')
                    ->setCellValue('G'.$i,date('Y-m-d H:i:s', $v['finishtime']))
                    ->setCellValue('H'.$i,empty($v['pay_time']) ? "" :date('Y-m-d H:i:s', $v['pay_time']))
                    ->setCellValue('I'.$i,($v['pay_status']==1)?'未支付':'已支付')
                    ->setCellValue('J'.$i,$v['pay_uname'])
                    ->setCellValue('K'.$i,$v['pay_staff_num'])
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
            $filename =isset($user)?$user->username.'('.$user->name.'提成支付':'提成支付';
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
        $non = Orders::find()->select(["sum(money) as num"])->where(["pay_status"=>1])->asArray()->one();
        $paed = Orders::find()->select(["sum(money) as num"])->where(["pay_status"=>2])->asArray()->one();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'non'=>$non,
            'paed'=>$paed

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

    /** 财务支付（批量，单个）*/
    public function actionPay()
    {
        $uid = Yii::$app->user->identity->id;    //获取当前登录人id
        
        if (isset($_GET['ids'])) 
        {
            $ids = explode(',', $_GET['ids']);
            foreach ($ids as $k => $v) 
            {
                if ($this->orderStatus($v) == 1)/*获取该订单的状态 1为未支付 2为已支付*/
                {
                    $data = Orders::findOne(['id' => $v]);
                    if ($data) 
                    {
                        $push_uid = User::find()->where(['username'=>$data['staff_num']])->one();
                        $push[] = (string)$push_uid->id;
                        $pay_money = $data['money'];
                        /*将每个人的支付金额求和存入OrdersSum start*/
                         $staff_num = strval($data["staff_num"]);
                        $ordersSum1 = OrdersSum::findOne(["staff_num" => $staff_num]);
                        
                        if($ordersSum1 != null)//判断off_orders_sum中是否有这个人
                        {
                            $pay_order_sum = $ordersSum1->pay_order_sum;
                            $sum = strval($pay_order_sum + $pay_money);
                            $ordersSum1->pay_order_sum = $sum ;
                            $ordersSum1->save();
                        }
                        else
                        {
                            $ordersSum2 = new OrdersSum(); 
                            $ordersSum2->staff_num = strval($staff_num);
                            $ordersSum2->pay_order_sum = strval($pay_money);
                            $ordersSum2->save();
                        }
                        /*将每个人的支付金额求和存入OrdersSum end*/
                        $data->pay_status = '2';
                        $data->pay_time = time();
                        $data->pay_uid = $uid;
                        $data->pay_money = $pay_money;
                        if (!$data->save() && !$ordersSum->save()   ) 
                        {
                            return $this->error('支付失败','index');
                        }
                    }
                }
            }
            /*推送用户 id的别名*/
            $res = $this->Jpush(array_unique($push));
            if ($res) {
                foreach ($push as $key=>$value)
                {
                    //添加日志
                    $add = $this->jpushLog($value, '支付推送');
                }
                if ($add){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
            /*推送用户 id的别名*/
        } 
        /*单个支付*/
        if(isset($_GET['id'])) {
            $data = Orders::findOne(['id' => $_GET['id']]);

            if($data)
            {
                $pay_money = $data["money"];
            }
            /*将每个人的支付金额求和存入OrdersSum start*/
             $staff_num = strval($data["staff_num"]);
            $ordersSum1 = OrdersSum::findOne(["staff_num" => $staff_num]);
            if($ordersSum1 != null)//判断off_orders_sum中是否有这个人
            {
                $pay_order_sum = $ordersSum1->pay_order_sum;
                $sum = strval($pay_order_sum + $pay_money);
                $ordersSum1->pay_order_sum = $sum ;
                $ordersSum1->save();
            }
            else
            {
                $ordersSum2 = new OrdersSum(); 
                $ordersSum2->staff_num = strval($staff_num);
                $ordersSum2->pay_order_sum = strval($pay_money);
                $ordersSum2->save();
            }
            /*将每个人的支付金额求和存入OrdersSum end*/
            $data->pay_status = '2';
            $data->pay_time = time();
            $data->pay_uid = $uid;
            $data->pay_money = $pay_money;
            if (!$data->save()) {
               return $this->error("支付失败！","index",1);
            } else {
	            /*推送用户 id的别名*/
	                $push_uid = User::find()->where(['username'=>$staff_num])->one();
	                $res = $this->Jpush((string)$push_uid->id);
	                if ($res) {
	                    $add = $this->jpushLog($push_uid->id, '支付推送');
	                    if ($add){
	                        return $this->success('支付成功','index',1);
	                    }else{
	                        return $this->error('添加日志失败','index',1);
	                    }
	                }else{
	                    return $this->error('推送失败！','index',1);
	                }
	            /*推送用户 id的别名*/
            }
        }
    }
    /**
     * @param $receive  接收推送的对象
     * @return bool
     * 极光推送
     */
    public function Jpush($receive)
    {
        $app_key = Yii::$app->params['jpush_appkey'];
        $master_secret = Yii::$app->params['jpush_secret'];
        $client = new Client($app_key, $master_secret);
        $push = $client->push();
        $push->setPlatform(['ios', 'android']);
        $push->addAlias($receive);
        $push->androidNotification('您有一笔新的收入！', ['extras' =>['badge'=>'1']]);
        $push->iosNotification('您有一笔新的收入！', ['sound' => 'sound', 'badge' => '+1']);
        $res = $push->send();
        if ($res['http_code'] == 200){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $receive  推送 接收人
     * @param $content  推送内容
     * @return bool
     * 推送添加日志
     */
    public function jpushLog($receive, $content)
    {
        $log = new JpushLog();
        $log->receive = $receive;
        $log->time = time();
        $log->content = $content;
        if ($log->save()){
            return true;
        }else{
            return false;
        }
    }
    /*
    *选中求和
    */
    public function actionGetSum()
    {
        if (isset($_GET['ids'])) 
        {
            $ids = explode(',', $_GET['ids']);
            foreach ($ids as $k => $v) 
            {
                
                if ($this->orderStatus($v) == 1)/*获取该订单的状态 1为未支付 2为已支付*/
                {
                    $data = Orders::findOne(['id' => $v]);
                    if ($data) 
                    {
                        $pay_money = $data['money'];
                        $arr[] =  $pay_money;  
                    }
                }
            }
            return array_sum($arr);
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
        return $data['pay_status'];

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
}
