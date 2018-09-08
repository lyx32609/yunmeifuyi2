<?php

namespace backend\controllers;

use backend\models\User;
use backend\models\UserDepartment;
use Yii;
use backend\models\BindCount;
use backend\models\BindCountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;

require(__DIR__ . '/../../vendor/excel/PHPExcel.php');

/**
 * BindCountController implements the CRUD actions for BindCount model.
 */
class BindCountController extends Controller
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
     * Lists all BindCount models.
     * @return mixed
     */
    public function actionIndex()
    {

        if (Yii::$app->request->get('select'))//查询
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('BindCountSearch');
            } else {
                $data = \Yii::$app->request->get('BindCountSearch');
            }

            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $searchModel = new BindCountSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->Where(['between', 'time', $start_time, $end_time]);
            $dataProvider->query->orderBy('time desc');

            //如果账号不为空
            if (!empty($username)) {
                $dataProvider->query->andWhere(['local_count' => $username]);
            }
            //如果姓名不为空
            if (!empty($name)) {

                $u_data = User::find()
                    ->select('username')
                    ->Where(['name' => $name])
                    ->asArray()
                    ->one();
                $dataProvider->query->andWhere(['local_count' => $u_data['username']]);
            }
            //判断当前登录账号是否为超管
            if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                $department = "";//可以看到全部的部门
            }
            else{
                $company = Yii::$app->user->identity->company_categroy_id;
                if(!$company){
                    echo "<script>alert('当前登录账号公司id不存在！');history.back()</script>";
                    return false;
                }
                $department_data = UserDepartment::find()
                    ->select('id')
                    ->where(['company'=>$company])
                    ->asArray()
                    ->all();
                $department_ids = array_column($department_data,'id');
//                var_dump(array_column($department_data,'id'));die;
                $department = ["in", "local_department", $department_ids];
            }
            $dataProvider->query->andWhere($department);
            //按照部门查找
            if (!empty($department_id)) {
                $dataProvider->query->andWhere(['local_department' => $department_id]);
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
                $data = \Yii::$app->request->post('BindCountSearch');
            } else {
                $data = \Yii::$app->request->get('BindCountSearch');
            }

            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['department']) ? "" : $data['department'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $model = BindCount::find()
                ->select('b.*,u.name as local_name,u.phone,d.name as dname')
                ->from(BindCount::tableName() . ' as b')
                ->leftJoin(User::tableName() . ' as u', 'b.local_count=u.username')
                ->leftJoin(UserDepartment::tableName() . ' as d', 'd.id = b.local_department')
                ->orderBy('b.time desc')
                ->where(['between', 'b.time', $start_time, $end_time])
            ;
//            var_dump($model);die;
            //如果账号不为空
            if (!empty($username)) {
                $model->andWhere(['b.local_count' => $username]);
            }
            //如果姓名不为空
            if (!empty($name)) {

                $u_data = User::find()
                    ->select('username')
                    ->Where(['name' => $name])
                    ->asArray()
                    ->one();
                $model->andWhere(['local_count' => $u_data['username']]);
            }
            //判断当前登录账号是否为超管
            if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                $department = "";//可以看到全部的部门
            }
            else{
                $company = Yii::$app->user->identity->company_categroy_id;
                if(!$company){
                    echo "<script>alert('当前登录账号公司id不存在！');history.back()</script>";
                    return false;
                }
                $department_data = UserDepartment::find()
                    ->select('id')
                    ->where(['company'=>$company])
                    ->asArray()
                    ->all();
                $department_ids = array_column($department_data,'id');
//                var_dump(array_column($department_data,'id'));die;
                $department = ["in", "local_department", $department_ids];
            }
            $model->andWhere($department);

            //按照部门查找
            if (!empty($department_id)) {
                $model->andWhere(['local_department' => $department_id])->asArray()->all();
//                var_dump($data);die;
            }
            $data = $model->asArray()->all();
            foreach ($data as $k=>$v){
                $other_department = $v['other_department'];     //关联部门
                $operation_id = $v['operation_id'];             //操作人ID
                $other_data = $this->getData($operation_id,$other_department);
                if (!$other_data){
                    echo "<script>alert('操作人账号姓名不存在！');history.back()</script>";
                    return false;
                }
                $data[$k]['operation_name'] = $other_data['name'];          //操作人姓名
                $data[$k]['operation_count'] = $other_data['username'];     //操作人账号
                $data[$k]['other_dname'] = $other_data['department_name'];  //关联部门名称
                $data[$k]['time'] = date("Y-m-d H:i:s",$v['time']);

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
            // $objPHPExcel->getActiveSheet()->setTitle(isset($user)?$user->name.'打卡记录':'人员打卡记录');
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')用户关联':'用户关联');
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
                ->setCellValue('A'.$i, '云管理账号')
                ->setCellValue('B'.$i, '姓名')
                ->setCellValue('C'.$i, '部门')
                ->setCellValue('D'.$i, '手机号')
                ->setCellValue('E'.$i, '关联部门')
                ->setCellValue('F'.$i, '关联账号')
                ->setCellValue('G'.$i, '关联时间')
                ->setCellValue('H'.$i, '操作人')
                ->setCellValue('I'.$i, '操作人账号')
                ->setCellValue('J'.$i, '操作内容');
            //循环获取数据
            $i = 3;

            foreach ($data as $v)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,$v['local_count'])
                    ->setCellValue('B'.$i,$v['local_name'])
                    ->setCellValue('C'.$i,$v['dname'])
                    ->setCellValue('D'.$i,$v['phone'])
                    ->setCellValue('E'.$i,$v['other_dname'])
                    ->setCellValue('F'.$i,$v['other_count'])
                    ->setCellValue('G'.$i,$v['time'])
                    ->setCellValue('H'.$i,$v['operation_name'])
                    ->setCellValue('I'.$i,$v['operation_count'])
                    ->setCellValue('J'.$i,$v['operation_content'])
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
            $filename =isset($user)?$user->username.'('.$user->name.'用户关联':'用户关联';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();

        }


        $searchModel = new BindCountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BindCount model.
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
     * Creates a new BindCount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BindCount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing BindCount model.
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
     * Deletes an existing BindCount model.
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
     * Finds the BindCount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BindCount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BindCount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
     *账号关联*/
    public function actionBind(){

        $local_count = $_GET['local_count'];
        $other_count = $_GET['other_count'];
        $other_department = $_GET['other_department'];
        if ($local_count&&$other_count&&$other_department){
            $data = User::find()
                ->select('username')
                ->where(['username'=>$local_count])
                ->asArray()
                ->one();
            $bind_data = BindCount::find()
                ->select('local_count')
                ->where(['local_count'=>$local_count])
                ->asArray()
                ->one();
            $bind_count = $bind_data['local_count'];
            $username = $data['username'];
            $user_id = Yii::$app->user->identity->id;
            if(!$username){
                echo "<script>alert('您输入的云管理账号不存在！');history.back()</script>";
                return false;
            }
            if($bind_count){
                echo "<script>alert('您输入的云管理账号已关联！');history.back()</script>";
                return false;
            }
            $local_department = User::find()
                ->select('department_id')
                ->where(['username'=>$local_count])
                ->asArray()
                ->one();
            $local_department = $local_department['department_id'];

            $bind_modle = new BindCount();
            $bind_modle->local_count = $local_count;
            $bind_modle->other_count = $other_count;
            $bind_modle->other_department = $other_department;
            $bind_modle->local_department = $local_department;
            $bind_modle->operation_id = $user_id;
            $bind_modle->operation_content = '账号关联';
            $bind_modle->time = time();
            if(!$bind_modle->save()){
                echo "<script>alert('关联失败！');history.back()</script>";
                return false;
            }
        }
        return $this->redirect(['index']);

    }

    /*
     * 获取关联账号的部门和用户名*/
    private function getData($user_id,$department_id){
        $user_data = User::find()
            ->select('name,username')
            ->where(['id'=>$user_id])
            ->asArray()
            ->one();
        $department_data = UserDepartment::find()
            ->select('name')
            ->where(['id'=>$department_id])
            ->asArray()
            ->one();
        if(!$user_data || !$department_data){
            return false;
        }
        $data['name'] = $user_data['name'];
        $data['username'] = $user_data['username'];
        $data['department_name'] = $department_data['name'];
        return $data;
    }
}
