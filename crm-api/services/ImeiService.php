<?php


namespace app\services;


use app\foundation\Service;
use app\models\PutImei;
use app\models\User;
use app\models\UserDepartment;
use yii\data\Pagination;


class ImeiService extends Service
{
    /**
     * 获取某公司的手机设备记录列表
     * @param $company_categroy_id  公司ID
     * @return mixed
     */
    public function getImeiList($company_categroy_id,$page=1,$pageSize=10,$start_time,$end_time)
    {
        if(!$company_categroy_id){
            $this->setError('公司ID不能为空');
            return false;
        }
        if(empty($start_time) || empty($end_time)){
            $imei_data = PutImei::find()
                ->where(['company_categroy_id' => $company_categroy_id])
                ->select(["id","department_id","user_id","submit_time","status",'is_read'])
                ->orderBy('submit_time desc');
        }else{
            $start_time .= " 0:0:0";
            $end_time .= " 23:59:59";
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $imei_data = PutImei::find()
                ->where(['company_categroy_id' => $company_categroy_id])
                ->andWhere(['between','submit_time',$start_time,$end_time])
                ->select(["id","department_id","user_id","submit_time","status",'is_read'])
                ->orderBy('submit_time desc');
        }

        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageSize,
            'totalCount' => $imei_data->count(),
        ]);

        $data = $imei_data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()->all();
        if(!$data){
            $this->setError('该时间范围内公司暂无申请列表信息');
            return false;
        }
        foreach ($data as $key => $v)
        {
            $result[$key]['id'] = $v['id'];                               //申请设备记录ID
            $result[$key]['user_id'] = $v['user_id'];                     //申请人ID
            $result[$key]['status'] = $v['status'];                       //状态
            $result[$key]['is_read'] = $v['is_read'];                     //1为未阅读状态，2为已阅读状态
            $result[$key]['time'] = date('Y-m-d',$v['submit_time']);
            $result[$key]['department_name'] = $this->getDepartment($v['department_id']);
            if(!$result[$key]['department_name']){
                $result[$key]['department_name'] ='';
            }
            $result[$key]['user_name'] = $this->getUserName($v['user_id']);
        }

        return ['list'=>$result, 'pageCount'=>$pagination->pageCount];

    }
    /**
     * 获取用户姓名
     * @param $user_id 用户ID
     * @return mixed
     */
    private function getUserName($user_id)
    {
        $data = User::find()
            ->select('name')
            ->where(['id'=>$user_id])
            ->asArray()
            ->one();
        return $data['name'];
    }

    /**
     * 获取部门名称
     * @param $department_id    部门ID
     * @return mixed
     */
    private function getDepartment($department_id)
    {
        $data = UserDepartment::find()
            ->select('name')
            ->where(['id'=>$department_id])
            ->asArray()
            ->one();
        return $data['name'];
    }

    /**
     * @param $user_id
     * 获取详情列表（个人提报历史记录）
     */
    public function imeiDetails($user_id,$start_time,$end_time)
    {
        if(empty($start_time) || empty($end_time)){
            $record_data = PutImei::find()
                ->select(['id','old_brand','submit_time','new_brand','old_submit_time','is_read','status'])
                ->where(['user_id'=>$user_id])
                ->orderBy('id desc')
                ->asArray()
                ->all();
        }else{
            $start_time .= " 0:0:0";
            $end_time .= " 23:59:59";
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $record_data = PutImei::find()
                ->select(['id','old_brand','submit_time','new_brand','old_submit_time','is_read','status'])
                ->where(['user_id'=>$user_id])
                ->andWhere(['between','submit_time',$start_time,$end_time])
                ->orderBy('submit_time desc')
                ->asArray()
                ->all();
        }
        if(!$record_data){
            $this->setError('该时间范围内用户暂无详情信息');
            return false;
        }
        foreach($record_data as $key => $v){
            $data[$key]['id'] = $v['id'];                                  //现在的手机品牌
            $data[$key]['status'] = $v['status'];                                  //审核状态
            $data[$key]['now_brand'] = $v['new_brand'];                                  //现在的手机品牌
            $data[$key]['old_brand'] = $v['old_brand'];                                  //现在的手机品牌
            $data[$key]['submit_time'] = date('Y-m-d',$v['submit_time']);        //用户提交新设备的时间
            $data[$key]['old_submit_time'] = date('Y-m-d',$v['old_submit_time']);//原设备用户提交时间


            if($v['is_read'] == 1){
                $this->updateReadStutas($v['id']);                                              //修改已读状态
            }
        }
        return $data;
    }

    /**
     * 修改阅读状态
     * @param $id    off_put_imei 表中的ID
     * @return bool
     */
    private function updateReadStutas($id)
    {
        $result = PutImei::find()
            ->where(['id'=>$id])
            ->one();
        $result->is_read = '2';//'2'表示已读
        if(!$result->save())
        {
            $this->setError('是否已读状态更改失败');
            return false;
        }
    }

    /**
     * 新设备提报
     * @param $imei
     * @param $brand
     * @param $user_id
     * @param $company_categroy_id
     * @return bool
     */
    public function updateNewImei($imei,$brand,$user_id,$company_categroy_id){
        if(!$imei){
            $this->setError('手机设备号不能为空');
            return false;
        }
        if(!$brand){
            $this->setError('手机品牌不能为空');
            return false;
        }
        if(!$user_id){
            $this->setError('用户ID不能为空');
            return false;
        }

        $imei_data = User::find()
            ->select(['id','phone_imei','imei_time','phone_brand','department_id'])
            ->where(['id'=>$user_id])
            ->asArray()
            ->one();
        $user_data =User::find()
            ->where(['phone_imei'=>$imei])
            ->asArray()
            ->all();
        if($user_data[0]['id']==$user_id){
            $this->setError('该手机设备已绑定本账号');
            return false;
        }
        elseif($user_data){
            $this->setError('该手机设备已绑定其他账号');
            return false;
        }

        $PutImei = new PutImei();

        $PutImei->user_id = $user_id;
        $PutImei->new_imei_number = $imei;
        $PutImei->submit_time = time();
        $PutImei->department_id = $imei_data['department_id'];
        $PutImei->old_imei_number = $imei_data['phone_imei'];
        $PutImei->status = '1';       //1 为未审状态  2为审核状态
        $PutImei->old_brand = $imei_data['phone_brand'];
        $PutImei->old_submit_time = $imei_data['imei_time'];
        $PutImei->new_brand = $brand;
        $PutImei->company_categroy_id = $company_categroy_id;
        $PutImei->is_read = '1';     //默认1为未读状态
        if (!$PutImei->save()){
            $this->setError('更新设备申请失败');
            return false;

        }
        return true;

    }


}