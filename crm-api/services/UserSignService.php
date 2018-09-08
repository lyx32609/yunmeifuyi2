<?php
namespace app\services;

use app\foundation\Service;

use app\models\Member;
use app\models\Supplier;
use app\models\UserSign;
use app\models\UserWork;
use app\models\UserWorkSign;
use Yii;
class UserSignService extends Service
{
    /**
     *  获取员工签到记录
     * @param  [type] $user    用户ID
     * @param  [type] $time    开始时间
     * @param  [type] $endTime 结束时间
     * @return [type]  array
     */
    public function getHistroySign($user, $startTime, $endTime)
    {
        if(!$user) {
             
            $this->setError('用户不能为空');
            return false;
        }
        if(!$startTime||!$endTime) {
            $this->setError('时间不能为空');
            return false;
        }
        $result = UserSign::find()
        ->select(['type', 'time', 'path'])
        ->where('user = :user',[':user' => $user])
        ->andWhere(['between', 'time', $startTime, $endTime])
        ->orderBy('time desc')
        ->asArray()
        ->all()
        ;
        if(!$result) {
            $this->setError('没有人员签到信息');
            return false;
        }
        return $result;
    }
    /**
     * 员工签到
     * @param varchar $user：提交人   decimal $longitude：签到经度    decimal $latitude：签到纬度    
     * @return msg："保存成功",result 
     */
    public function add($user, $type, $longitude, $latitude, $image, $company_categroy_id, $path)
    {
        if(!$type){
            $this->setError('签到不能为空!');
            return false;
        }
        if(!$longitude){
            $this->setError('经度不能为空!');
            return false;
        }
        if(!$latitude){
            $this->setError('纬度不能为空!');
            return false;
        }
        $data = strtotime(date("Y-m-d"),time());
        $sign_time = time() - $data;
        $work_time_one = UserWork::findOne(['status' => 1, 'company_id' => $company_categroy_id]); //查询上午上下班时间
        if(!$work_time_one){
            $result = [
                'ret' => 28,
                'msg' => '请联系人资，添加上下班时间'
            ];
            return $result;
        }
        $work_time_two = UserWork::findOne(['status' => 2, 'company_id' => $company_categroy_id]); //查询下午上下班时间
        if(!$work_time_two){
            $result = [
                'ret' => 28,
                'msg' => '请联系人资，添加上下班时间'
            ];
            return $result;
        }
        $user_sign_num = UserSign::find()
                ->select('id')
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data, time()])
                ->asArray()
                ->all();
        if(!$user_sign_num){
            $type = 1;
        } else {
            $type = count($user_sign_num) % 2 ? 2 : 1;
        }
        if($type == 1){
            if($sign_time < $work_time_one->to_work){ //如果打卡时间小于上午上班时间
                $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 0,
                            'is_late_time' => '0',
                        );
            } else if(($sign_time > $work_time_one->to_work) && ($sign_time < $work_time_one->go_work)){//如果打卡时间大于上午上班时间但是小于上午下班时间
                $user_sign_time = UserSign::find()
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data, $data + $work_time_one->go_work])
                ->andWhere(['type' => 1])
                ->asArray()
                ->one();
                if(!$user_sign_time){//如果上午下班班之前没有打卡
                    if($sign_time > $work_time_one->to_work){ //如果打卡时间大于上午上班时间
                        $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 1,
                            'is_late_time' => strval(ceil(($sign_time - $work_time_one->to_work) / 60)),
                        );
                   } else {
                       $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 0,
                            'is_late_time' => 0,
                        );
                    }
                } else {//如果上午上班之前有打卡
                    $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 0,
                            'is_late_time' => '0',
                        );
                }
            } else if($sign_time > $work_time_two->to_work){//如果打卡时间大于下午上班时间
                $user_sign_time = UserSign::find()
                        ->where(['user' => $user])
                        ->andWhere(['between', 'time', $data + $work_time_one->go_work, time()])
                        ->andWhere(['type' => 1])
                        ->orderBy('time desc')
                        ->asArray()
                        ->one();
                if(!$user_sign_time){//如果上午下班之后到打卡时没有记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 1,
                        'is_late_time' => strval(ceil(($sign_time - $work_time_two->to_work) / 60)),
                    );
                } else {//如果上午下班之后到打卡时有记录
                    $user_sign_time_one = UserSign::find()
                            ->where(['user' => $user])
                            ->andWhere(['between', 'time', $data + $work_time_one->to_work, time()])
                            ->andWhere(['type' => 1])
                            ->asArray()
                            ->one();
                    if(!$user_sign_time_one){//如果下午上班到打卡时没有记录
                        $user_sign_time_one = UserSign::find()
                                ->select(['time'])
                                ->where(['user' => $user])
                                ->andWhere(['between', 'time', $data + $work_time_one->go_work, $data + $work_time_two->to_work])
                                ->orderBy('time desc')
                                ->andWhere(['type' => 1])
                                ->asArray()
                                ->one();
                        if(!$user_sign_time_one){//如果中午打卡无记录
                            $columns = array(
                                'user'=>$user,
                                'type'=>$type,
                                'time'=>time(),
                                'longitude' => $longitude,
                                'latitude' => $latitude,
                                'image' => $image,
                                'path' => $path,
                                'company_id' => $company_categroy_id,
                                'is_late' => 1,
                                'is_late_time' => strval(ceil(($sign_time - $work_time_two->to_work) / 60)),
                            );
                        } else {//如果中午打卡次数超过2次
                            $columns = array(
                                'user'=>$user,
                                'type'=>$type,
                                'time'=>time(),
                                'longitude' => $longitude,
                                'latitude' => $latitude,
                                'image' => $image,
                                'path' => $path,
                                'company_id' => $company_categroy_id,
                                'is_late' => 0,
                                'is_late_time' => '0',
                            );
                        }
                    } else {//如果下午上班到打卡时有记录
                        $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 0,
                            'is_late_time' => '0',
                        );
                    }
                }
            } else {
                $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 0,
                            'is_late_time' => '0',
                        );
            }
        } else {
            $columns = array(
                'user'=>$user,
                'type'=>$type,
                'time'=>time(),
                'longitude' => $longitude,
                'latitude' => $latitude,
                'image' => $image,
                'path' => $path,
                'company_id' => $company_categroy_id,
                'is_late' => 0,
                'is_late_time' => '0',
            );
        }
        
        $rs=\Yii::$app->dbofficial->createCommand()->insert('off_user_sign', $columns)->execute();
       if(!$rs){
           if($type == '1'){
               $this->setError('签到失败!');
           } else if($type == '2'){
               $this->setError('签退失败!');
           }
           
           return false;
       }
       if ($type==1)
       {
          return [msg=>'签到成功'];
       }
       if ($type==2)
       {
          return [msg=>'签退成功'];
       }
    }
    /**
     * 判断该用户今日是否有打卡记录
     * @param unknown $user
     * @param unknown $start
     * @param unknown $end
     * @param unknown $type
     * @return boolean
     */
    public function getSign($user, $start, $end, $type)
    {
        $result = UserSign::find()
                ->where(['between', 'time', $start, $end])
                ->andWhere(['user' => $user])
                ->andWhere(['type' => $type])
                ->asArray()
                ->one();
        if(!$result){
            return false;
        }
        return true;
    }
    
    /* 
     * 扫码方式签到签退
     * @param varchar $user：提交人   $identity_id：对应表id    $identity：表识别
     *  
     *  $identity 来识别表   1是 member  2是 supplier
     *  
     *  */
    public function scanAdd($user,$type,$identity_id,$identity_type)
    {
        
        if(!$type){
            $this->setError('签到不能为空!');
            return false;
        }
        if(!$identity_id){
            $this->setError('身份ID不能为空!');
            return false;
        }
        if(!$identity_type){
            $this->setError('身份不能为空!');
            return false;
        }
        
        
        if($identity_type==1)
        {
           // $member=Member::findOne(['member_id'=>$identity_id]);
            $member=Yii::$app->api->request('basic/getMember',['member_id'=>$identity_id]);
            if($member&&$member['ret']==0)
            {
                $longitude=$member[0]['longitude'];
                $latitude=$member[0]['latitude'];

            }else{
                $this->setError('采购商信息获取失败');
                return false;
            }
            
        }
        elseif($identity_type==2)
        {
           // $supplier=Supplier::findOne(['uid'=>$identity_id]);
            $supplier=Yii::$app->api->request('basic/getSupplier',['supplierId'=>$identity_id]);
            if($supplier&&$supplier['ret']==0)
            {
                $longitude=$member[0]['longitude'];
                $latitude=$member[0]['latitude'];

            }else{
                $this->setError('供货商信息获取失败');
                return false;
            }
        }
        $columns=array(
            'user'=>$user,
            'type'=>$type,
            'time'=>time(),
            'longitude' => $longitude,
            'latitude' => $latitude,
            'identity_id'=>$identity_id,
            'identity_type'=>$identity_type,
            'sign_type'=>2,
        );
        $rs=\Yii::$app->dbofficial->createCommand()->insert('off_user_sign', $columns)->execute();
        if(!rs){
            $this->setError('签到失败!');
            return false;
        }
        if ($type==1)
        {
            return [msg=>'签到成功'];
        }
        if ($type==2)
        {
            return [msg=>'签退成功'];
        }
    }
    /**
     * 员工签到
     * @param varchar $user：提交人   decimal $longitude：签到经度    decimal $latitude：签到纬度
     * @return msg："保存成功",result
     */
    public function addNew($user, $longitude, $latitude, $image, $company_categroy_id, $path)
    {
    
        if(!$longitude){
            $this->setError('经度不能为空!');
            return false;
        }
        if(!$latitude){
            $this->setError('纬度不能为空!');
            return false;
        }
        $data = strtotime(date("Y-m-d"),time());
        $sign_time = time() - $data;
        $user_work = UserWorkSign::findOne(['is_staff' => 2, 'company_id' => $company_categroy_id]); //查询上午上下班时间
        if(!$user_work){
            $result = [
                'ret' => 28,
                'msg' => '请联系人资，添加上下班时间'
            ];
            return $result;
        }
        $type = 0;
        if($user_work->status == '1'){ //如果企业忽略上午下班时间及下午上班时间
            if($sign_time < $user_work->morning_to_work){ //如果打卡时间小于上午上班时间
                $columns = array(
                    'user'=>$user,
                    'type'=>$type,
                    'time'=>time(),
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'image' => $image,
                    'path' => $path,
                    'company_id' => $company_categroy_id,
                    'is_late' => 0,
                    'is_late_time' => '0',
                );
            } else {//如果打卡时间大于上午上班时间
                $result = UserSign::find()
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data, time()])
                ->asArray()
                ->one();
                if($result){ //如果零点到打卡时有记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 0,
                        'is_late_time' => '0',
                    );
                } else {//如果零点到打卡时无记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 1,
                        'is_late_time' => strval(ceil(($sign_time - $user_work->morning_to_work) / 60)),
                    );
                }
            }
        } else { //如果企业不忽略上午下班时间及下午上班时间
            if(($sign_time < $user_work->morning_to_work) || (($sign_time > $user_work->morning_go_work) && ($sign_time < $user_work->after_to_work))){ //如果打卡时间小于上午上班时间 或者 打卡时间大于上午下班时间且小于下午上班时间
                $columns = array(
                    'user'=>$user,
                    'type'=>$type,
                    'time'=>time(),
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'image' => $image,
                    'path' => $path,
                    'company_id' => $company_categroy_id,
                    'is_late' => 0,
                    'is_late_time' => '0',
                );
            } else if(($sign_time > $user_work->morning_to_work) && ($sign_time < $user_work->morning_go_work)) {//如果打卡时间大于上午上班时间且小于上午下班时间
                $result = UserSign::find()
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data, time()])
                ->asArray()
                ->one();
                if($result){ //如果零点到打卡时有记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 0,
                        'is_late_time' => '0',
                    );
                } else {//如果零点到打卡时无记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 1,
                        'is_late_time' => strval(ceil(($sign_time - $user_work->morning_to_work) / 60)),
                    );
                }
            } else if($sign_time > $user_work->after_to_work){ //如果打卡时间大于下午上班时间
                $result = UserSign::find()
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data + $user_work->morning_go_work, $data + $user_work->after_to_work])
                ->asArray()
                ->all();
                if(count($result) > 2){ //
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 0,
                        'is_late_time' => '0',
                    );
                } else {//如果中午打卡未超过2次
                    $result_one = UserSign::find()
                    ->where(['user' => $user])
                    ->andWhere(['between', 'time', $data + $user_work->after_to_work, time()])
                    ->asArray()
                    ->one();
                    if($result_one){ //如果下午上班之后有打卡记录
                        $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 0,
                            'is_late_time' => '0',
                        );
                    } else { //如果下午上班之后无打卡记录
                        $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 1,
                            'is_late_time' => strval(ceil(($sign_time - $user_work->after_to_work) / 60)),
                        );
                    }
                }
            }
        }
    
        $rs=\Yii::$app->dbofficial->createCommand()->insert('off_user_sign', $columns)->execute();
        if(!$rs){
            $this->setError('打卡失败!');
            return false;
        }
        return ['msg' => '打卡成功'];
         
    }
    /**
     * 员工签到
     * @param varchar $user：提交人   decimal $longitude：签到经度    decimal $latitude：签到纬度
     * @return msg："保存成功",result
     */
    public function addNewOne($user, $longitude, $latitude, $image, $company_categroy_id, $path, $remarks)
    {
    
        if(!$longitude){
            $this->setError('经度不能为空!');
            return false;
        }
        if(!$latitude){
            $this->setError('纬度不能为空!');
            return false;
        }
        $data = strtotime(date("Y-m-d"),time());
        $sign_time = time() - $data;
        $user_work = UserWorkSign::findOne(['is_staff' => 2, 'company_id' => $company_categroy_id]); //查询上午上下班时间
        if(!$user_work){
            $result = [
                'ret' => 28,
                'msg' => '请联系人资，添加上下班时间'
            ];
            return $result;
        }
        $type = 0;
        if($user_work->status == '1'){ //如果企业忽略上午下班时间及下午上班时间
            if($sign_time < $user_work->morning_to_work){ //如果打卡时间小于上午上班时间
                $columns = array(
                    'user'=>$user,
                    'type'=>$type,
                    'time'=>time(),
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'image' => $image,
                    'path' => $path,
                    'company_id' => $company_categroy_id,
                    'is_late' => 0,
                    'is_late_time' => '0',
                );
            } else {//如果打卡时间大于上午上班时间
                $result = UserSign::find()
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data, time()])
                ->asArray()
                ->one();
                if($result){ //如果零点到打卡时有记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 0,
                        'is_late_time' => '0',
                    );
                } else {//如果零点到打卡时无记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 1,
                        'is_late_time' => strval(ceil(($sign_time - $user_work->morning_to_work) / 60)),
                    );
                }
            }
        } else { //如果企业不忽略上午下班时间及下午上班时间
            if(($sign_time < $user_work->morning_to_work) || (($sign_time > $user_work->morning_go_work) && ($sign_time < $user_work->after_to_work))){ //如果打卡时间小于上午上班时间 或者 打卡时间大于上午下班时间且小于下午上班时间
                $columns = array(
                    'user'=>$user,
                    'type'=>$type,
                    'time'=>time(),
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'image' => $image,
                    'path' => $path,
                    'company_id' => $company_categroy_id,
                    'is_late' => 0,
                    'is_late_time' => '0',
                );
            } else if(($sign_time > $user_work->morning_to_work) && ($sign_time < $user_work->morning_go_work)) {//如果打卡时间大于上午上班时间且小于上午下班时间
                $result = UserSign::find()
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data, time()])
                ->asArray()
                ->one();
                if($result){ //如果零点到打卡时有记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 0,
                        'is_late_time' => '0',
                    );
                } else {//如果零点到打卡时无记录
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 1,
                        'is_late_time' => strval(ceil(($sign_time - $user_work->morning_to_work) / 60)),
                    );
                }
            } else if($sign_time > $user_work->after_to_work){ //如果打卡时间大于下午上班时间
                $result = UserSign::find()
                ->where(['user' => $user])
                ->andWhere(['between', 'time', $data + $user_work->morning_go_work, $data + $user_work->after_to_work])
                ->asArray()
                ->all();
                if(count($result) > 2){ //
                    $columns = array(
                        'user'=>$user,
                        'type'=>$type,
                        'time'=>time(),
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'image' => $image,
                        'path' => $path,
                        'company_id' => $company_categroy_id,
                        'is_late' => 0,
                        'is_late_time' => '0',
                    );
                } else {//如果中午打卡未超过2次
                    $result_one = UserSign::find()
                    ->where(['user' => $user])
                    ->andWhere(['between', 'time', $data + $user_work->after_to_work, time()])
                    ->asArray()
                    ->one();
                    if($result_one){ //如果下午上班之后有打卡记录
                        $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 0,
                            'is_late_time' => '0',
                        );
                    } else { //如果下午上班之后无打卡记录
                        $columns = array(
                            'user'=>$user,
                            'type'=>$type,
                            'time'=>time(),
                            'longitude' => $longitude,
                            'latitude' => $latitude,
                            'image' => $image,
                            'path' => $path,
                            'company_id' => $company_categroy_id,
                            'is_late' => 1,
                            'is_late_time' => strval(ceil(($sign_time - $user_work->after_to_work) / 60)),
                        );
                    }
                }
            }
        }
        if($remarks) {
            $columns['remarks'] = $remarks;
        }
        $rs=\Yii::$app->dbofficial->createCommand()->insert('off_user_sign', $columns)->execute();
        if(!$rs){
            $this->setError('打卡失败!');
            return false;
        }
        return ['msg' => '打卡成功'];
         
    }
}