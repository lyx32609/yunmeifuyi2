<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 16:14
 */
namespace app\services;

use app\foundation\Service;
use app\models\Examine;
use app\models\User;
use app\models\Petition;
use app\models\Regions;

class DetailPetitionNewService extends Service
{
    /**
     * @param $petition_id
     * 发起的签呈详情
     * @return array|bool
     */
    public function detailPetition($petition_id,$user_id)
    {
        if(!$petition_id) {
            $this->setError('签呈ID不能为空');
            return false;
        }
        //签呈的详情
        $query = Petition::find()
            ->select(['message','master_img','file','status','ids','type','source'])
            ->where('id =:petition_id',[':petition_id'=>$petition_id])
            ->one();

        //查询审批进程包含加签之后的顺序
        $arr_ids  = explode(',',$query['ids']);
        //查询加签的进程
        $result = $this->FindAdd($arr_ids,$petition_id);
        //查询审批进程
        $data = $this->FindExamine($arr_ids, $petition_id);

        return ['detail'=>$query,'list1'=>$result,'list'=>$data];
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
            ->select(['off_user.name','off_petition.message','off_petition.invalid_description','off_petition.master_img','off_petition.file','off_petition.create_time','off_petition.ids','off_petition.status','off_petition.source','off_petition.uid','off_petition.type'])
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
}