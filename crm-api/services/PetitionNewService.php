<?php
namespace app\services;

use app\foundation\Service;
use app\models\Examine;
use app\models\User;
use app\models\Petition;
use yii\data\Pagination;
use app\models\Regions;

class PetitionNewService extends Service
{
    /*
     * 员工的发起签呈详情
     * */
    public function detailPetition($petition_id)
    {
        if(!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        $query = Petition::find()
            ->select(['message','master_img','file'])
            ->where('id =:petition_id',[':petition_id'=>$petition_id])
            ->All();
        //关联 user 表和 petition表查询  进程列表的信息
        $data = Examine::find()
            ->joinWith(['user'])
            ->joinWith(['petition'])
            ->select('off_user.name,off_examine.status,off_examine.examine_time,advice')
            ->where('petition_id =:petition_id',[':petition_id'=>$petition_id])
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
            unset($data[$key]['user']);
            unset($data[$key]['petition']);
        }
        return ['detail'=>$query[0],'list'=>$data];
    }
    /*
     * 主管接收签呈详情
     * */
    public function ManageDetailReceive($user_id,$petition_id)
    {
        if(!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        //model中需要加上关联的user表
        $query = Petition::find()
            ->joinWith(['user'])
            ->joinWith(['examine'])
            ->select(['off_user.name','off_petition.message','off_petition.master_img','off_petition.file','off_petition.create_time','off_user.id'])
            ->where('off_petition.id =:petition_id',[':petition_id'=>$petition_id])
            ->asArray()
            ->All();
        $status = Examine::find()->select('status')->andWhere("petition_id=$petition_id")->andWhere("uid=$user_id")->asArray()->one();
        //查询部门
        $uid = $query[0]['id'];
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
            $query[$key]['status'] = $status['status'];
            unset($query[$key]['examine']);
            unset($query[$key]['user']);
        }
        //关联 user 表和 petition表查询  进程列表的信息
        $data = Examine::find()
            ->joinWith(['user'])
            ->joinWith(['petition'])
            ->select('off_user.name,off_examine.status,off_examine.examine_time,advice')
            ->where('petition_id =:petition_id',[':petition_id'=>$petition_id])
            ->asArray()
            ->all();
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
            unset($data[$key]['user']);
            unset($data[$key]['petition']);
        }
        return ['detail'=>$query[0],'list'=>$data];
    }
    public function listPetition($user_id, $page_count,$page_size)
    {
        $people = User::find()
            ->andWhere('id = :user',[':user'=>$user_id])
            ->one();
        if(!$people)
        {
            $this->setError('员工不存在!');
            return false;
        }
        $query = Petition::find()
            ->select(['type','create_time','status','id'])
            ->where('uid =:staffId',[':staffId'=>$user_id])
            ->andWhere(['is_show'=>1])
            ->orderBy('create_time DESC');
        //分页参数
        $pages = new Pagination([
            'params'=>['page'=>$page_count],
            'defaultPageSize' => $page_size,
            'totalCount' => $query->count(),
        ]);
        $total_page = ceil($query->count()/$page_size);

        $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        foreach ($model as $key=> $value)
        {
            $model[$key]['create_time'] = date('Y-m-d H:i:s',$model[$key]['create_time']);
        }

        return ['list'=>$model,'total_page'=>$total_page];;

    }
    /*
     *主管签呈列表  status 1 是发起的签呈 2是接收的签呈
     * */
    public function managePetition($status,$user_id,$page_size,$page_count)
    {
        if (!$user_id) {
            $this->setError('用户ID不能为空');
            return false;
        }
        if (!$status) {
            $this->setError('状态不可为空');
            return false;
        }
        if (!$page_size) {
            $this->setError('每页显示条数不可为空');
            return false;
        }
        if (!$page_count) {
            $this->setError('页码不可为空');
            return false;
        }
        //发起的签呈
        if ($status == 1) {
            $post = $this->listPetition($user_id,$page_count,$page_size);
            return $post;
        }
        //接收的签呈
        if ($status == 2){
            $receive = Examine::find()
                ->joinWith(['petition'])
                ->select(['off_petition.id','off_petition.type','off_petition.create_time','off_petition.status'])
                ->andWhere('off_examine.uid =:uid',[':uid'=>$user_id])
                ->andWhere('off_examine.is_visible = 1')
                ->andWhere(['off_petition.is_show'=>1])
                ->orderBy('off_petition.create_time DESC')
                ->asArray();
            //分页参数
            $pages = new Pagination([
                'params'=>['page'=>$page_count],
                'defaultPageSize' => $page_size,
                'totalCount' => $receive->count(),
            ]);
            $total_page = ceil($receive->count()/$page_size);
            $model = $receive->offset($pages->offset)->limit($pages->limit)->all();

            foreach ($model as $key=> $value)
            {
                $model[$key]['create_time'] = date('Y-m-d H:i:s',$model[$key]['create_time']);
                unset($model[$key]['petition']);
            }
            return ['list' =>$model,'total_page'=>$total_page];
        }

    }









}