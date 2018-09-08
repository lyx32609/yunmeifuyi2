<?php

namespace backend\controllers;

use Yii;
use backend\models\WithdSetting;
use backend\models\WithdSettingSeach;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
use backend\models\User;
use backend\models\UserDepartment;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');

/**
 * WithdSettingController implements the CRUD actions for WithdSettinsg model.
 */
class WithdSettingController extends Controller
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
     * Lists all WithdSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('select'))//查询
        {

            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('WithdSettingSeach');
            } else {
                $data = \Yii::$app->request->get('WithdSettingSeach');
            }
            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['set_department_id']) ? "" : $data['set_department_id'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $searchModel = new WithdSettingSeach();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->Where(['between', 'set_time', $start_time, $end_time]);
            $dataProvider->query->orderBy('set_time desc');

            //如果账号不为空
            if (!empty($username)) {
                $dataProvider->query->andWhere(['username' => $username]);
            }
            //如果姓名不为空
            if (!empty($name)) {

                $u_data = User::find()
                    ->select('id')
                    ->Where(['name' => $name])
                    ->asArray()
                    ->one();
                $dataProvider->query->andWhere(['set_uid' => $u_data['id']]);
            }
            //按照部门查找
            if (!empty($department_id)) {
                $dataProvider->query->andWhere(['set_department_id' => $department_id]);
            }

            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = date('Y-m-d', $start_time);
            $searchModel->end_time = date('Y-m-d', $end_time);
            $searchModel->username = $username;
            $searchModel->name = $name;
            $searchModel->set_department_id = $department_id;

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);

        } elseif (Yii::$app->request->get('export'))//导出
        {
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('WithdSettingSeach');
            } else {
                $data = \Yii::$app->request->get('WithdSettingSeach');
            }

            $username = $data['username'];              //账号
            $name = $data['name'];                      //姓名
            $department_id = empty($data['set_department_id']) ? "" : $data['set_department_id'];     //部门
            $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7); //开始时间
            $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime(); //结束时间

            $model = WithdSetting::find()
                ->select('w.set_uid,w.set_before,w.set_after,w.set_time,w.set_cont,w.set_department_id,u.name,u.username,d.name as department')
                ->from(WithdSetting::tableName() . ' as w')
                ->leftJoin(User::tableName() . ' as u', 'w.set_uid = u.id')
                ->leftJoin(UserDepartment::tableName() . ' as d', 'd.id = w.set_department_id')
                ->orderBy('w.set_time desc')
                ->where(['between', 'w.set_time', $start_time, $end_time]);
            //如果账号不为空
            if (!empty($username)) {
                $model->andWhere(['u.username' => $username]);

            }
            if (!empty($name)) {
                $model->andWhere(['u.name' => $name]);

            }
            //按照部门查找
            if (!empty($department_id)) {
                $model->andWhere(['w.set_department_id' => $department_id]);

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
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, isset($user)?$user->username.'('.$user->name.')提现费率':'提成支付');
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
                ->setCellValue('D'.$i, '操作内容')
                ->setCellValue('E'.$i, '修改前')
                ->setCellValue('F'.$i, '修改后')
                ->setCellValue('G'.$i, '操作时间');
            //循环获取数据
            $i = 3;

            foreach ($data as $v)
            {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i,$v['username'])
                    ->setCellValue('B'.$i,$v['name'])
                    ->setCellValue('C'.$i,$v['department'])
                    ->setCellValue('D'.$i,$v["set_cont"])
                    ->setCellValue('E'.$i,$v['set_before'])
                    ->setCellValue('F'.$i,$v['set_after'])
                    ->setCellValue('G'.$i,date('Y-m-d H:i:s', $v['set_time']));
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
            $filename =isset($user)?$user->username.'('.$user->name.'提现费率':'提现费率';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();

        }


        $searchModel = new WithdSettingSeach();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WithdSetting model.
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
     * Creates a new WithdSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WithdSetting();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WithdSetting model.
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
     * Deletes an existing WithdSetting model.
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
     * Finds the WithdSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WithdSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WithdSetting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
