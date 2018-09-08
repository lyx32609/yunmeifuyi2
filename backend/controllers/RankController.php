<?php

namespace backend\controllers;

use Yii;
use backend\models\UserIndex;
use backend\models\UserIndexSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RankController implements the CRUD actions for UserIndex model.
 */
class RankController extends Controller
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
     * Lists all UserIndex models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserIndexSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      //  print_r($dataProvider);exit();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserIndex model.
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
     * Creates a new UserIndex model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserIndex();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserIndex model.
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
     * Deletes an existing UserIndex model.
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
     * Finds the UserIndex model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserIndex the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($username)
    {
        if(!$username)
        {
            $this->setError('用户名不能为空');
            return false;
        }
        
        /*         //本日   暂时用不到留着备用
         $Dstime = mktime(0,0,0,date('m'),date('d'),date('Y'));
         $Detime = mktime(23,59,59,date('m'),date('d'),date('Y'));
         //本周
         $Wstime = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
         $Wetime = mktime(23,59,59,date('m'),date('d')-date('w')+6,date('Y'));
         //本月
         $Mstime = mktime(0,0,0,date('m'),1,date('Y'));
         $Metime = mktime(23,59,59,date('m'),date('t'),date('Y')); */
        //判断当前员工是否有统计记录
         $userindex = UserIndex::find()
        ->where(['userid'=>$username])
        ->count();
        if($userindex != 0){
            //查询出员工的统计数据
            $department = UserIndex::find()
            ->select('userid,SUM(visitingnum) as visitingnum,SUM(registernum) as registernum , SUM(ordernum) as ordernum ,SUM(orderamount) as orderamount ,SUM(orderuser) as orderuser,SUM(deposit) as deposit , SUM(maimaijinorder) as maimaijinorder ,SUM(maimaijinamount) as maimaijinamount ,SUM(maimaijinuser) as maimaijinuser')
            ->groupBy('userid')
            ->orderBy('userid asc')
            ->asArray()
            ->all();
            if(count($department) >0){
                $rs_data[] = $this->getStatistical($department,$username,'visitingnum','拜访客户');
                $rs_data[] = $this->getStatistical($department,$username,'registernum','累计注册量');
                $rs_data[] = $this->getStatistical($department,$username,'registernum','累计自己注册');
                $rs_data[] = $this->getStatistical($department,$username,'ordernum','累计订单数量');
                $rs_data[] = $this->getStatistical($department,$username,'orderamount','累计订单金额');
                $rs_data[] = $this->getStatistical($department,$username,'orderuser','累计订单用户数量');
                $rs_data[] = $this->getStatistical($department,$username,'deposit','累计预存款订金额');
                $rs_data[] = $this->getStatistical($department,$username,'maimaijinorder','累计买买金订单量');
                $rs_data[] = $this->getStatistical($department,$username,'maimaijinamount','累计买买金订单金额');
                $rs_data[] = $this->getStatistical($department,$username,'maimaijinuser','累计买买金订单用户量');
            }
        }else{
            //统计所有的员工
            $countNum = UserIndex::find()
            ->groupBy('userid')
            ->count();
            $rs_data = $this->getStatisticalZero($countNum);
        
        }
        //print_r($rs_data);exit();
        return $rs_data; 
        
        
        
/*          if (($model = UserIndex::findOne(99)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }  */
    }
    
    
    //计算统计排行
    public function getStatistical($arr,$username,$index,$indexName){
        //获取客户排名
        $return_data = $this->my_sort($arr, $index);
        foreach($return_data as $k=>$v)
        {
            if($v['userid'] == $username)
            {
                $rs_data['rank'] = $k+1;
                $rs_data['num'] = $v[$index];
                $rs_data['typeName'] = $indexName;
            }
        }
        return $rs_data;
    }
    
    
    //二维数组排序方法
    function my_sort($arrays,$sort_key,$sort_order=SORT_DESC,$sort_type=SORT_NUMERIC  )
    {
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }
    
    

    //返回为0的统计数组
    public function getStatisticalZero($countNum){
        $rsdata = Array
        (
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '拜访客户',
                ),
    
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '累计注册量',
                ),
    
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '累计自己注册',
                ),
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '累计订单数量',
                ),
    
            Array
            (
                'rank' => $countNum,
                'num' => 0,
                'typeName' => '累计订单金额',
                ),
    
            Array
            (
                'rank' =>$countNum,
                'num' => 0,
                'typeName' => '累计订单用户数量',
                ),
    
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '累计预存款订金额',
                ),
    
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '累计买买金订单量',
                ),
    
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '累计买买金订单金额',
                ),
    
            Array
            (
                'rank' => $countNum,
                'num' =>  0,
                'typeName' => '累计买买金订单用户量',
                )
    
            );
        return $rsdata;
    }
    
}
