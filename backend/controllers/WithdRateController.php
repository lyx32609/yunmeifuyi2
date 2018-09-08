<?php

namespace backend\controllers;

use Yii;
use backend\models\WithdRate;
use backend\models\WithdRateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use components\helpers\DateHelper;
use backend\models\WithdSetting;

/**
 * WithdRateController implements the CRUD actions for WithdRate model.
 */
class WithdRateController extends Controller
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

    public function actionSet()
    {

        $data = Yii::$app->request->get();//提交的数据
        $rate_model = WithdRate::findOne(['id'=>1]);//历史数据
        $setting_model = new WithdSetting;//操作记录
        $set_uid = Yii::$app->user->identity->id;//操作人id
        $set_department_id = Yii::$app->user->identity->department_id;//操作人id

        $setting_model->set_uid = $set_uid;
        $setting_model->set_time = time();
        $setting_model->set_department_id = $set_department_id;
        //如果有历史数据
        if($rate_model)
        {
                /*修改手续费*/
                if(count($data["pound_money"]) == 2)
                {
                    $pound_money = $data["pound_money"][0];

                    $setting_model->set_before = $rate_model->pound_money.'元';//操作前
                    $rate_model->pound_money = $pound_money;
                    $setting_model->set_after = $pound_money.'元';//操作后
                    $setting_model->set_cont = '修改提现手续费';

                    
                }
                /*修改费率*/
                if(count($data["pound_percent"]) == 2)
                {
                    $pound_percent = $data["pound_percent"][0];

                    $setting_model->set_before = $rate_model->pound_percent.'%';//操作前
                    $rate_model->pound_percent = $pound_percent;
                    $setting_model->set_after = $pound_percent.'%';//操作后
                    $setting_model->set_cont = '修改提现费率';
                }
                /*开关手续费*/
                if($data['is_open'][0])
                {
                    $setting_model->set_before = ($rate_model->is_open == 1) ? '开启'. $rate_model->is_open_which: '关闭'.$rate_model->is_open_which;//操作前

                    $rate_model->is_open = $data['is_open'][0];
                    $rate_model->is_open_which = $data['is_open_which'];

                    $setting_model->set_after = ($data['is_open'][0] == 1) ? '开启'. $data['is_open_which']: '关闭'.$data['is_open_which'];//操作后
                    $setting_model->set_cont = ($data['is_open'][0] == 1) ? '开启收取手续费': '关闭收取手续费';
                }
                /*修改最低可转出金额*/
                if(count($data["transferable_out_money"]) == 2)
                {
                    $transferable_out_money = $data["transferable_out_money"][0];

                    $setting_model->set_before = $rate_model->transferable_out_money.'元';//操作前
                    $rate_model->transferable_out_money = $transferable_out_money;
                    $setting_model->set_after = $transferable_out_money.'元';//操作后
                    $setting_model->set_cont = '修改最低可转出金额';
                }
                /*开关最低转出金额限制*/
                if($data['is_open_transferable_out'][0])
                {
                   
                    $setting_model->set_before = ($rate_model->is_open_transferable_out == 1) ? '开启': '关闭';//操作前

                    $rate_model->is_open_transferable_out = $data['is_open_transferable_out'][0];

                    $setting_model->set_after = ($data['is_open_transferable_out'][0] == 1) ? '开启': '关闭';//操作后
                    $setting_model->set_cont = ($data['is_open_transferable_out'][0] == 1) ? '开启最低可转出限制': '关闭最低可转出限制';
                }
        }
        //没有历史数据
        else
        {
             $rate_model = new WithdRate;
                /*修改手续费*/
                if(count($data["pound_money"]) == 2)
                {
                    $pound_money = $data["pound_money"][0];

                    $setting_model->set_before = '0';//操作前
                    $rate_model->pound_money = $pound_money;
                    $setting_model->set_after = $pound_money.'元';//操作后
                    $setting_model->set_cont = '修改提现手续费';

                    
                }
                /*修改费率*/
                if(count($data["pound_percent"]) == 2)
                {
                    $pound_percent = $data["pound_percent"][0];

                    $setting_model->set_before = '0';//操作前
                    $rate_model->pound_percent = $pound_percent;
                    $setting_model->set_after = $pound_percent.'%';//操作后
                    $setting_model->set_cont = '修改提现费率';
                }
                /*开关手续费*/
                if($data['is_open'][0])
                {
                    $setting_model->set_before = '关闭';//操作前

                    $rate_model->is_open = $data['is_open'][0];
                    $rate_model->is_open_which = $data['is_open_which'];

                    $setting_model->set_after = ($data['is_open'][0] == 1) ? '开启'. $data['is_open_which']: '关闭'.$data['is_open_which'];//操作后
                    $setting_model->set_cont = ($data['is_open'][0] == 1) ? '开启收取手续费': '关闭收取手续费';
                }
                /*修改最低可转出金额*/
                if(count($data["transferable_out_money"]) == 2)
                {
                    $transferable_out_money = $data["transferable_out_money"][0];

                    $setting_model->set_before = '0';//操作前
                    $rate_model->transferable_out_money = $transferable_out_money;
                    $setting_model->set_after = $transferable_out_money.'元';//操作后
                    $setting_model->set_cont = '修改最低可转出金额';
                }
                /*开关最低转出金额限制*/
                if($data['is_open_transferable_out'][0])
                {
                   
                    $setting_model->set_before = '关闭';//操作前

                    $rate_model->is_open_transferable_out = $data['is_open_transferable_out'][0];

                    $setting_model->set_after = ($data['is_open_transferable_out'][0] == 1) ? '开启': '关闭';//操作后
                    $setting_model->set_cont = ($data['is_open_transferable_out'][0] == 1) ? '开启最低可转出限制': '关闭最低可转出限制';
                }
        }
        
        if($rate_model->save())
        {
            $setting_model->save();
        }
        return $this->redirect(['withd-setting/index']);

    }
    /**
     * Displays a single WithdRate model.
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
     * Creates a new WithdRate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WithdRate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WithdRate model.
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
     * Deletes an existing WithdRate model.
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
     * Finds the WithdRate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WithdRate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WithdRate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
