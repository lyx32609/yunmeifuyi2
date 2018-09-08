<?php

namespace backend\controllers;

use Yii;
use backend\models\Shop;
use backend\models\ShopSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
require(__DIR__ . '/../../vendor/excel/PHPExcel.php');

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class ShopController extends Controller
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
     * Lists all Shop models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->get('select')){

            $searchModel = new ShopSearch();
            $params = Yii::$app->request->queryParams;
            $data = \Yii::$app->request->get('ShopSearch');
            $data['start_time'] = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7);
            $data['end_time'] = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime();
            if ($data['shop_type'] == 0){
                $data['shop_type'] = '';
            }
            $shopname = $data["shop_name"];
            $username = $data["user_name"];
            $name = $data["name"];
            $shop_addr = $data["shop_addr"];
            $phone = $data["phone"];
            $shoptype = $data["shop_type"];
            //查询后查询条件依然显示在前端页面
            $searchModel->start_time = $data['start_time'];
            $searchModel->end_time = $data['end_time'];
            $searchModel->shop_name = $shopname;
            $searchModel->name = $name;
            $searchModel->shop_addr = $shop_addr;
            $searchModel->phone = $phone;
            $params['ShopSearch'] = $data;

            $dataProvider = $searchModel->search($params);
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }elseif (Yii::$app->request->get('export')){  //导出
            $searchModel = new ShopSearch();
            if (!empty(Yii::$app->request->post())) {
                $data = \Yii::$app->request->post('ShopSearch');
            } else {
                $data = \Yii::$app->request->get('ShopSearch');
            }
            if ($data['shop_type'] == 0){
                $data['shop_type'] = '';
            }
//            echo '<pre>';
//            var_dump($data);die;
            $starttime = !empty($data['start_time']) ? strtotime($data['start_time']) : '';
            $endtime = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : '';
            $shopname = $data["shop_name"];
            $username = $data["user_name"];
            $name = $data["name"];
            $shop_addr = $data["shop_addr"];
            $phone = $data["phone"];
            $shoptype = $data["shop_type"];

          $searchModel->shop_name = $shopname;
          $searchModel->user_name = $username;
          $searchModel->name = $name;
          $searchModel->shop_addr = $shop_addr;
          $searchModel->phone = $phone;
          $searchModel->start_time = $starttime;
          $searchModel->end_time = $endtime;

          $model = Shop::find();
          if (!empty($shopname)){
              $model->andFilterWhere(['like', 'shop_name', $shopname]);
          }
          if (!empty($username)){
              $model->andFilterWhere(['like', 'user_name', $username]);
          }
          if (!empty($name)){
              $model->andFilterWhere(['like', 'name', $name]);
          }
          if (!empty($phone)){
              $model->andFilterWhere(['like', 'phone', $phone]);
          }
          if (!empty($shop_addr)){
              $model->andFilterWhere(['like', 'shop_addr', $shop_addr]);
          }
          if (!empty($shoptype)){
              $model->andFilterWhere(['shop_type'=>$shoptype]);
          }
          if (!empty($starttime) || !empty($endtime)){
              $model->andFilterWhere(['between', 'createtime', $starttime, $endtime]);
          }
          $model =   $model->asArray()->all();
//            echo '<pre>';
//            var_dump($model);die;
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
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, '客户信息列表');
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
                ->setCellValue('B' . $i, '联系人')
                ->setCellValue('C' . $i, '联系方式')
                ->setCellValue('D' . $i, '业务员姓名')
                ->setCellValue('E' . $i, '客户类型')
                ->setCellValue('F' . $i, '客户地址')
                ->setCellValue('G' . $i, '新增时间');
            //循环获取数据
            $i = 3;
            foreach ($model as $v) {
                if ($v['shop_type'] == 1){
                    $type = '生产商';
                }elseif ($v['shop_type'] == 2){
                    $type = '供货商';
                }elseif ($v['shop_type'] == 3){
                    $type = '采购商';
                }elseif ($v['shop_type'] == 4){
                    $type = '配送商';
                }elseif ($v['shop_type'] == 5){
                    $type = '店铺商';
                }elseif ($v['shop_type'] == 6){
                    $type = '运营商';
                }elseif ($v['shop_type'] == 7){
                    $type = '销售商';
                }elseif ($v['shop_type'] == 8){
                    $type = '服务商';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $v['shop_name'])
                    ->setCellValue('B' . $i, $v['name'])
                    ->setCellValue('C' . $i, $v['phone'])
                    ->setCellValue('D' . $i, $v['user_name'])
                    ->setCellValue('E' . $i, $type)
                    ->setCellValue('F' . $i, $v['shop_addr'])
                    ->setCellValue('G' . $i, date('Y-m-d H:i:s', $v['createtime']) );
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
            $filename = '客户信息列表';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');
//            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit();

        }
        $searchModel = new ShopSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shop model.
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
     * Creates a new Shop model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Shop();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Shop model.
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
     * Deletes an existing Shop model.
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
     * Finds the Shop model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Shop the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shop::findOne($id)) !== null) {
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
        $data = Shop::findOne(['id' => $_GET['id']]);

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
