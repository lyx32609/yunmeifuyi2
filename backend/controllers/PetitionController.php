<?php

namespace backend\controllers;

use backend\models\Examine;
use function React\Promise\all;
use Yii;
use backend\models\Petition;
use backend\models\PetitionSearch;
use backend\models\CompanyCategroy;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use backend\models\User;
use components\helpers\DateHelper;
use backend\models\UploadForm;
use yii\web\UploadedFile;
use app\foundation\MulUpload;
use app\services\DetailPetitionNewService;
use backend\models\Regions;
use components\BaseController;
use backend\models\UserDepartment;
use common\components\Upload;
use yii\helpers\Json;
// use backend\models\Upload;



/**
 * PetitionController implements the CRUD actions for Petition model.
 */
class PetitionController extends BaseController
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
                    // 'delete' => ['POST','GET'],
                    // 'index' => ['POST','GET'],
                    // 'create'=> ['POST','GET'],
                    // 'update' => ['POST','GET'],
                    // 'get-company'=> ['POST','GET'],
                    // 'get-department'=> ['POST','GET'],
                    // 'get-company'=> ['POST','GET'],
                    // 'get-petition'=> ['POST','GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all Petition models.
     * @return mixed
     */
    public function actionIndex()
    {
        $user_id = Yii::$app->user->identity->id;
        //点击查询按钮后进行查询
        if(Yii::$app->request->get('select'))
        {
            if (!empty(Yii::$app->request->post()))
            {
                $data = \Yii::$app->request->post('PetitionSearch');
            }
            else
            {
                $data = \Yii::$app->request->get('PetitionSearch');
            }
            $searchModel = new PetitionSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $start_time =! empty($data['start_time'])?strtotime($data['start_time']):DateHelper::getDayStartTime(7);
            $end_time =! empty($data['end_time'])?(strtotime($data['end_time'])+86399):DateHelper::getTodayEndTime();
            $status = $data["status"];
            $flag = $data["flag"];
            if((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或者超级管理员
            {   
                    $province = $data["province"];
                    $domain_id = $data["domain_id"];
                    $company_id = $data['company_categroy_id'];
                    $department = empty($data['department_id']) ? "" : $data['department_id'];
                    $username = $data["username"];
                    $name = $data["name"];
                    if(!empty($company_id)  && $company_id != '请选择公司' )
                    {
                        $dataProvider->query->andWhere(['off_user.company_categroy_id' => $company_id]);
                    }
                    if(!empty($department)  && $department != '请选择部门' )
                    {
                        $dataProvider->query->andWhere(['off_user.department_id' => $department]);
                    }
                    if(!empty($username))
                    {
                        $user = User::findOne(['username'=>$username]);
                        if($user)
                        {
                            $dataProvider->query->andWhere(['username' => $username]);
                        } else
                        {
                            echo '<script>alert("用户不存在");history.back()</script>';
                        }
                    }
                    if(!empty($name))
                    {
                        $userdata = User::findOne(['name'=>$name]);
                        if($userdata)
                        {
                            $dataProvider->query->andWhere(['name'=>$name]);
                        } else
                        {
                            echo '<script>alert("用户不存在");history.back()</script>';
                        }
                    }
            }

            if (!empty($start_time) && !empty($end_time)){
                $dataProvider->query->andWhere(['between','off_petition.create_time',$start_time,$end_time]);
            }
            if(!empty($status))
            {
                switch($status)
                {
                    case 1:$dataProvider->query->andWhere(['in','off_petition.status',[2,3]]);
                    break;
                    case 2:$dataProvider->query->andWhere(['off_petition.status'=>5]);
                    break;
                    case 3:$dataProvider->query->andWhere(['off_petition.status'=>6]);
                    break;
                    case 4:$dataProvider->query->andWhere(['in','off_petition.status',[0,1,4]]);
                    break;
                    case 5:$dataProvider->query->andWhere(['off_petition.status'=>7]);
                    break;
                }
            }
            if(!empty($flag))
            {
                if($flag == 1)
                {
                    $dataProvider->query->andWhere(['off_petition.uid'=>$user_id]);
                }
                else
                {
                    $examine  = Examine::find()->select(["petition_id"])->where(["uid"=>$user_id])->asArray()->all();
                    foreach($examine as $v)
                    {
                        $ids[] = $v["petition_id"];
                    }
                    $dataProvider->query->andWhere(['in','off_petition.id',$ids]);
                }
            }
            $dataProvider->query->andWhere(['is_show'=>1])->orderBy("off_petition.create_time desc");
            if((Yii::$app->user->identity->rank == 30) || in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))//总经理或超级管理员
            {
                $searchModel->username = $username;
                $searchModel->name = $name;
                $searchModel->province = $province;
                $searchModel->domain_id = $domain_id;
                $searchModel->company_categroy_id = $company_id;
                $searchModel->department_id = $department;
            }
            $searchModel->start_time = date('Y-m-d',$start_time);
            $searchModel->end_time = date('Y-m-d',$end_time);
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }


        //列表
        $searchModel = new PetitionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['is_show'=>1])
            ->orderBy("id desc");
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 签呈详情
     */
    public function actionView($id)
    {
        //0通用1领用2用车3付款4报销5采购6用证7用印8出差9加班10请假11外出12转正13离职14招聘
        $result = $this->detailPetitionReceive($id,Yii::$app->user->identity->id);
        $data = $result;
        switch ($result['detail']['type']) {
            case '0': return $this->render('/petition-view/common', ['data' => $data,]);    
                break;
            case '1': return $this->render('/petition-view/receive', ['data' => $data,]);    
                break;
            case '2': return $this->render('/petition-view/use-car', ['data' => $data,]);    
                break;
            case '3': return $this->render('/petition-view/payment', ['data' => $data,]);    
                break;
            case '4': return $this->render('/petition-view/reimburse', ['data' => $data,]);    
                break;
            case '5': return $this->render('/petition-view/purchase', ['data' => $data,]);    
                break;
            case '6': return $this->render('/petition-view/use-credit', ['data' => $data,]);
                break;
            case '7': return $this->render('/petition-view/seal', ['data' => $data,]);
                break;
            case '8': return $this->render('/petition-view/evection', ['data' => $data,]);    
                break;
            case '9': return $this->render('/petition-view/overtime', ['data' => $data,]);    
                break;
            case '10': return $this->render('/petition-view/leave', ['data' => $data,]);
                break;
            case '11': return $this->render('/petition-view/go-out', ['data' => $data,]);
                break;
            case '12': return $this->render('/petition-view/positive', ['data' => $data,]);
                break;
            case '13': return $this->render('/petition-view/dimission', ['data' => $data,]);
                break;
            case '14': return $this->render('/petition-view/recruit', ['data' => $data,]);    
                break;
            default:
            return $this->render('view1', ['data' => $data,]); 
                break;
        }
    }


    /*
    *签呈作废
    */
    public function actionInvalid($id)
    {
        $model = Petition::findOne($id);
        $model->status = 7;
        if ($model->save())
        {  
            return $this->success("作废成功",'index',3);
         
        }
        else
        {
           return $this->error("作废失败",'index',3);
        }
    }

    /*
    *签呈发布
    */
    public function actionCreate()
    {
        if(Yii::$app->request->get())
        {
            $get = Yii::$app->request->get();
            $type = empty($get['type']) ? 0 : $get["type"]; 
            switch($type)
            {
                //0通用1领用2用车3付款4报销5采购6用证7用印8出差9加班10请假11外出12转正13离职14招聘
                case '0': return $this->render('/petition-create/common'); //0通用 
                    break;
                case '1': return $this->render('/petition-create/receive'); //1领用 
                    break;
                case '2': return $this->render('/petition-create/use-car'); //2用车   
                    break;
                case '3': return $this->render('/petition-create/payment'); //3付款   
                    break;
                case '4': return $this->render('/petition-create/reimburse');//4报销    
                    break;
                case '5': return $this->render('/petition-create/purchase'); //5采购   
                    break;
                case '6': return $this->render('/petition-create/use-credit'); //6用证   
                    break;
                case '7': return $this->render('/petition-create/seal'); //7用印   
                    break;
                case '8': return $this->render('/petition-create/evection'); //8出差   
                    break;
                case '9': return $this->render('/petition-create/overtime'); //9加班   
                    break;
                case '10': return $this->render('/petition-create/leave'); //10请假
                    break;
                case '11': return $this->render('/petition-create/go-out'); //11外出
                    break;
                case '12': return $this->render('/petition-create/positive'); //12转正
                    break;
                case '13': return $this->render('/petition-create/dimission'); //13离职
                    break;
                case '14': return $this->render('/petition-create/recruit');//14招聘    
                    break;
                default:
                return $this->render('view1', ['data' => $data,]); 
                    break;
            }
        }elseif(Yii::$app->request->post())//接收数据
        {
            $data = Yii::$app->request->post();
            $ids = (count($data['ids']) > 1) ? join(',',$data['ids']) : $data['ids'][0];
            $data['master_img'] = isset($data['master_img']) ? $data['master_img'] : "";
            $data['file'] = isset($data['file']) ? $data['file'] : "";
            $petition_model = new Petition;
            if(in_array($data["type"],[1,7]) )
            {
                foreach($data['message']['message'] as $k=>$v)
                {
                    $data['message']['message'][$k] = join(",",$data['message']['message'][$k]);
                }
                $goods = join(";",$data['message']['message']);
                $data['message']['message'] = $goods;
            }
            if(in_array($data["type"],[2,8,9,10,11]) )
            {
                $data['message']['date'] = $data['message']['date']['date_start'].'——'.$data['message']['date']['date_end'];
            }
            if(in_array($data["type"],[6]) )
            {
                $data['message']['flag'] = join("",$data['message']['flag']);
            }
            if(in_array($data["type"],[14]) )
            {
                $data['message']['dutyreasonexplain'] = join("",$data['message']['dutyreasonexplain']);
                $data['message']['adjust'] = join("",$data['message']['adjust']);
            }
            $message  = json_encode($data['message']);
            $petition_model->master_img = $data['master_img'];
            $petition_model->file = $data['file'];
            $petition_model->uid = Yii::$app->user->identity->id;
            $petition_model->status = 3;
            $petition_model->ids = $ids;
            $petition_model->company_id = Yii::$app->user->identity->company_categroy_id;
            $petition_model->department_id = Yii::$app->user->identity->department_id;
            $petition_model->create_time = time();
            $petition_model->type = $data['type'];
            $petition_model->message = $message;
            $petition_model->source = 1;
            if($petition_model->save())
            {
                for($i=0;$i<count($data['ids']);$i++)
                {
                    $examine_model = new Examine;
                    $examine_model->petition_id = $petition_model->id;
                    $examine_model->uid = $data['ids'][$i];
                    $examine_model->status = '2';
                    $examine_model->flag = ($i == 0) ? 1 : 2;
                    $res = $examine_model->save();
                }
                if($res){
                        return $this->success("签呈提报成功","view?id=".$petition_model->id);
                }
                else{
                        return $this->error("签呈提报失败了，请重新提报！",'create');
                }
            }
            else
            {
                return $this->error("签呈提报失败，请重新提报！",'create');
            }
        }
        else//默认模板
        {
            return $this->render('/petition-create/common');
        }
    }

    /**
     * Updates an existing Petition model.
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
     * 删除签呈
     */
    public function actionDelete($id)
    {
        $petition = Petition::findOne($id);
        $petition->is_show = '0';
        $petition->save();
        if ($petition->save()){
            return $this->success('删除成功','index',3);
        }
        else
        {
            return $this->error('删除失败','index',3);
        }
    }
    /**
     * Finds the Petition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Petition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Petition::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * @param $petition_id
     * @return array|bool
     * 接收的签呈详情
     */
    public function detailPetitionReceive($petition_id,$user_id)
    {
        if(!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        /**
         * 签呈详情
         */
        $query = Petition::find()
            ->leftJoin('off_user','off_user.id = off_petition.uid')
            ->leftJoin('off_examine','off_examine.petition_id=off_petition.id')
            ->select(['off_user.name','off_petition.message','off_petition.master_img','off_petition.file','off_petition.create_time','off_petition.ids','off_petition.status','off_petition.uid','off_petition.type','off_petition.source'])
            ->where('off_petition.id =:petition_id',[':petition_id'=>$petition_id])
            ->asArray()
            ->All();

        $flag = Examine::find()
            ->select('flag, status')
            ->where(['petition_id'=>$petition_id])
            ->andWhere(['uid'=>$user_id])
            ->asArray()
            ->one();
        //查询部门
        $uid = $query[0]['uid'];
        $department = User::find()
            ->select('off_user_department.name')
            ->leftJoin('off_user_department','off_user_department.id=off_user.department_id' )
            ->where(['off_user.id' => $uid])
            ->asArray()
            ->all();
        //查询区域
        $p_region_id = User::find()
            ->select('off_regions.p_region_id')
            ->leftJoin('off_regions','off_regions.region_id=off_user.domain_id' )
            ->where(['off_user.id' => $uid])
            ->asArray()
            ->all();
        //查询区域
        $province = Regions::find()
            ->select('local_name')
            ->where(['region_id' => $p_region_id[0]['p_region_id']])
            ->asArray()
            ->all();
        //查询公司
        $company = User::find()
            ->select('off_company_categroy.name')
            ->leftJoin('off_company_categroy','off_company_categroy.id=off_user.company_categroy_id' )
            ->where(['off_user.id' => $uid])
            ->asArray()
            ->all();
        foreach ($query as $key =>$value)
        {
            if (empty($query[$key]['master_img'])){
                $query[$key]['master_img'] = '';
            }
            if (empty($query[$key]['file'])){
                $query[$key]['file'] = '';
            }
            $query[$key]['domain'] = $province[0]['local_name'] . $company[0]['name'] . $department[0]['name'];
            $query[$key]['create_time'] = date('Y-m-d H:i:s',$query[$key]['create_time']);
        }
        //审批人数组
        $arr_ids  = explode(',',$query[0]['ids']);
        // 加签进程
        $result = $this->FindAdd($arr_ids,$petition_id);
        // 审批进程
        $data = $this->FindExamine($arr_ids, $petition_id);
        return ['detail'=>$query[0],'list1'=>$result,'list'=>$data,'flag'=>$flag['flag'],'pay'=>$flag['status']];
    }


    /**
     * @param $arr_ids   审批人数组
     * @param $petition_id  签呈id
     * 查询加签人的进程列表
     * @return array
     */
    public function FindAdd($arr_ids, $petition_id)
    {
        //查询加签意见
        foreach ($arr_ids as $key =>$value)
        {
            $result1 =  Examine::find()
                ->leftJoin('off_user','off_user.id=off_examine.uid')
                ->leftJoin('off_user_department','off_user.department_id=off_user_department.id')
                ->leftJoin('off_company_categroy','off_user.company_categroy_id=off_company_categroy.id')
                ->select('off_user.name,off_examine.add_time,off_examine.add_advice,off_user_department.name as dname,off_company_categroy.name as cname')
                ->where('petition_id =:petition_id',[':petition_id'=>$petition_id])
                ->andWhere(['off_examine.uid'=>$value])
                ->andWhere(['!=','off_examine.add_advice',''])
                ->andWhere(['!=','off_examine.add_time',''])
                ->asArray()
                ->one();
            $result1['domain'] = $result1['cname'] . $result1['dname'];   //公司部门
            $result[] = $result1;
        }
        //去除审批人中 没有加签意见的null
        foreach ($result as $key=> $value)
        {
            if (!empty($result[$key]['add_time'])){
                $result[$key]['add_time'] = date('Y-m-d H:i:s',$result[$key]['add_time']);
            }
            if ($value['domain'] == null)
            {
                unset($result[$key]);
            }
            unset($result[$key]['cname']);
            unset($result[$key]['dname']);
        }
        // sort($result); //排序清除 unset 之后保留的key
        $result = array_values($result);
        return $result;
    }


    /**
     * @param $arr_ids   审批人数组
     * @param $petition_id  签呈id
     * 查询 审批进程
     * @return array
     */
    public function FindExamine($arr_ids, $petition_id)
    {
        //查询审批进程（包含加签之后的顺序）
        foreach ($arr_ids as $key =>$value)
        {
            $data1 =  Examine::find()
                ->leftJoin('off_user','off_user.id=off_examine.uid')
                ->leftJoin('off_petition','off_petition.id=off_examine.petition_id')
                ->leftJoin('off_user_department','off_user.department_id=off_user_department.id')
                ->leftJoin('off_company_categroy','off_user.company_categroy_id=off_company_categroy.id')
                ->select('off_user.name,off_examine.status,off_examine.examine_time,off_examine.advice,off_examine.tag,off_user_department.name as dname,off_company_categroy.name as cname')
                ->where('petition_id =:petition_id',[':petition_id'=>$petition_id])
                ->andWhere(['off_examine.uid'=>$value])
                ->asArray()
                ->one();
            $data1['domain'] = $data1['cname'] . $data1['dname'];     //公司 部门
            $data[] = $data1;
        }
        //更改状态的形式
        foreach ($data as $key=> $value)
        {
            if (empty($data[$key]['examine_time'])){
                $data[$key]['examine_time'] = '';
            }else{
                $data[$key]['examine_time'] = date('Y-m-d H:i:s',$data[$key]['examine_time']);
            }
            if (empty($data[$key]['advice'])){
                $data[$key]['advice'] = '';
            }
            if (empty($data[$key]['tag'])){
                $data[$key]['tag'] = '';
            }
            unset($data[$key]['cname']);
            unset($data[$key]['dname']);
        }
        return $data;
    }

    /*异步请求公司*/
    public function actionGetCompany()
    {

        $cid = Yii::$app->user->identity->company_categroy_id;
        $company  =  CompanyCategroy::find()
            ->select(["id",'name','fly'])
            ->where(["id"=>$cid])
            ->asArray()
            ->one();
        if($company["fly"] == 0)
        {
            $result[0]['id'] = $cid;
            $result[0]['name'] = $company["name"];
        }
        else
        {
            $company_parent = CompanyCategroy::findOne(company['fly']);
            $result[0]['id'] = $company_parent->id;
            $result[0]['name'] = $company_parent->name;
            $result[1]['id'] = $company['id'];
            $result[1]['name'] = $company['name'];
        }
        return json_encode($result);
    }

    /*异步请求部门*/
     public function actionGetDepartment()
     {
        $data = Yii::$app->request->get();
        $id = $data['id'];
        $result = UserDepartment::find()
                    ->select(["id","name","company"])
                    ->where(["company"=>$id])
                    ->asArray()
                    ->all();
        if($result)
        {
            return json_encode($result);
        }else{
            return json_encode("该公司没有部门");
        }
        
     }

     /*异步请求人员*/
     public function actionGetUser()
     {
        $data = Yii::$app->request->get();
        $did = $data['id'];
        $cid = $data["company"];
        $result = User::find()->select(["id","name"])
                ->where(["department_id"=>$did])
                ->andWhere(["company_categroy_id" => $cid])
                ->asArray()
                ->all();
        if($result)
        {
            return json_encode($result);
        }else{
            return json_encode("该部门暂时没有人员");
        }
     }
}
