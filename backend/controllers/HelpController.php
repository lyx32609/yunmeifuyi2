<?php

namespace backend\controllers;

use Yii;
use backend\models\Help;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HelpController implements the CRUD actions for Help model.
 */
class HelpController extends Controller
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
     * Lists all Help models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Help::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Help model.
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
     * Creates a new Help model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCreate()
    {
        $model = new Help();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dataProvider = new ActiveDataProvider([
                'query' => Help::find(),
            ]);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    public function actionCreate1()
    {
        $model = new Help();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dataProvider = new ActiveDataProvider([
                'query' => Help::find(),
            ]);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create1', [
                'model' => $model,
            ]);
        }
    }
    public function actionCreate2()
    {
        $model = new Help();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $dataProvider = new ActiveDataProvider([
                'query' => Help::find(),
            ]);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create2', [
                'model' => $model,
            ]);
        }
    }
    public function actionCreate3()
    {
        $model = new Help();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create3', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Help model.
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
     * Deletes an existing Help model.
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
     * Finds the Help model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Help the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Help::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionGetType(){
        $id = $_GET['id'];
        $result = Help::find()
            ->select(['id','type','content'])
            ->where(['id'=>$id])
            ->asArray()
            ->all();
        $data = '';
        if (count($result) > 0)
        {
            foreach ($result as $v)
            {
                $select = '';
                $data = "<option value='" . $v['type'] .  "' $select  >" . $v['content'] . "</option>";
            }
        }
        else
        {
            $data .= "<option>暂无类型</option>";
        }
        return $data;

    }
    public function actionGetSecond(){
        $type = $_GET['type'];
        $result = Help::find()
            ->select(['id','type','content'])
            ->where(['type'=>$type,'parent_id'=>0,'son_id'=>0])
            ->asArray()
            ->all();
//        var_dump($result);die;
        $data = '<option value="0">请选择分类</option>';
        if (count($result) > 0)
        {
            foreach ($result as $v)
            {
                $select = '';
                $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['content'] . "</option>";
            }
        }
        else
        {
            $data .= "<option>暂无二级分类</option>";
        }
        return $data;

    }
    public function actionGetThird(){
        $type = $_GET['type'];
        $result = Help::find()
            ->select(['id','type','content'])
            ->where(['parent_id'=>$type])
            ->andWhere(['type'=>0,'son_id'=>0])
            ->asArray()
            ->all();
        $data = '<option value="">请选择分类</option>';

        if (count($result) > 0)
        {
            foreach ($result as $v)
            {
                $select = '';
                $data .= "<option value='" . $v['id'] .  "' $select  >" . $v['content'] . "</option>";
            }
        }
        else
        {
            $data .= "<option>暂无三级分类</option>";
        }
        return $data;

    }
}
