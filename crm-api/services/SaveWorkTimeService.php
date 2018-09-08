<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserWorkSign;
use app\models\UserWork;

class SaveWorkTimeService extends Service
{
    /**
     * 设置工作时间（新）
     * @param unknown $status
     * @param unknown $morning_to_work
     * @param unknown $morning_go_work
     * @param unknown $company_id
     * @param unknown $uid
     * @param unknown $after_to_work
     * @param unknown $after_go_work
     */
    public function saveWorkTimeNew($status, $morning_to_work, $morning_go_work, $company_id, $uid, $after_to_work, $after_go_work)
    {
        if(!$status){
            $this->setError('是否忽略中午时间段不能为空');
            return false;
        }
        
        if(!$uid){
            $this->setError('用户id');
            return false;
        }
        if(!$company_id){
            $this->setError('公司id不能为空');
        }
        if(!$morning_to_work && !$after_to_work){
            $this->setError('时间区间不能为空');
            return false;
        }
        $user = User::findOne(['id' => $uid]);
        if(!$user){
            $this->setError('用户不存在');
        }
        $work_time = UserWorkSign::findOne(['company_id' => $company_id, 'is_staff' => 2]);
        if($work_time){
            $work_time->is_staff = 1;
            if(!$work_time->save()){
                $this->setError('状态改变失败');
                return false;
            }
        }
        $work = new UserWorkSign();
        $work->morning_to_work = $morning_to_work;
        if($status == '2'){
            $work->morning_go_work = $morning_go_work;
            $work->after_to_work = $after_to_work;
        } else {
            $work->morning_go_work = 99;
            $work->after_to_work = 99;
        }
        $work->after_go_work = $after_go_work;
        $work->create_time = time();
        $work->is_staff = 2;
        $work->user_name = $user->name;
        $work->company_id = $company_id;
        $work->status = $status;
        $work->uid = $uid;
        if(!$work->save()){
            $this->setError('工作时间设置失败');
            return false;
        }
        return $result = '工作时间设置成功';
    }
    /**
     * 设置工作时间
     * @param unknown $status
     * @param unknown $morning_to_work
     * @param unknown $morning_go_work
     * @param unknown $company_id
     * @param unknown $uid
     * @param unknown $after_to_work
     * @param unknown $after_go_work
     */
    public function saveWorkTime($company_categroy_id, $start, $end, $type)
    {
        $work_time = UserWork::findOne(['company_id' => $company_categroy_id, 'status' => $type]);
        if($work_time){
            $work_time->to_work = $start;
            $work_time->go_work = $end;
            if(!$work_time->save()){
                $this->setError('设置失败');
                return false;
            }
        } else {
            $result = new UserWork();
            $result->to_work = $start;
            $result->go_work = $end;
            $result->status = $type;
            $result->company_id = $company_categroy_id;
            if(!$result->save()){
                $this->setError('设置失败');
                return false;
            }
        }
        return ['msg' => '设置成功'];
    }
}