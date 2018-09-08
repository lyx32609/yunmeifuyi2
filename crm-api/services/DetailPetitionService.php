<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9
 * Time: 11:29
 */
namespace app\services;
use app\foundation\Service;
use app\models\Petition;
use app\models\Examine;
use app\models\User;
use app\models\Regions;

class DetailPetitionService extends Service
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
            ->select(['title','content','master_img','file'])
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
            if (empty($query[$key]['title'])){
                $query[$key]['title'] = '';
            }
            if (empty($query[$key]['content'])){
                $query[$key]['content'] = '';
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
            ->select(['off_user.name','off_petition.title','off_petition.content','off_petition.master_img','off_petition.file','off_petition.create_time','off_user.id'])
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
            if (empty($query[$key]['title'])){
                $query[$key]['title'] = '';
            }
            if (empty($query[$key]['content'])){
                $query[$key]['content'] = '';
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
}