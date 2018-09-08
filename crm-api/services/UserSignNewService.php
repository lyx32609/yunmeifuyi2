<?php
namespace app\services;

use app\foundation\Service;

use app\models\Member;
use app\models\Supplier;
use app\models\UserSign;
use Yii;
class UserSignNewService extends Service
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
        ->select(['type','time'])
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
    public function add($user,$type,$longitude,$latitude,$image, $path)
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
        if(!$path){
           $this->setError('地址不能为空!');
            return false; 
        }
        $result = new UserSign;
        $result->user = $user;
        $result->type = $type;
        $result->time = time();
        $result->longitude = $longitude;
        $result->latitude = $latitude;
        $result->image = $image;
        $result->path = $path;
        if(!$result->save()){
            //var_dump($result->getErrors());exit;
            if ($type==1)
           {
              return ['msg' => '签到失败'];
           }
           if ($type==2)
           {
              return ['msg' => '签退失败'];
           }
        } else {
            if ($type==1)
           {
              return [msg=>'签到成功'];
           }
           if ($type==2)
           {
              return [msg=>'签退成功'];
           }
        }
       // // echo '<pre>';print_r($columns);exit();
       //  $rs=\Yii::$app->dbofficial->createCommand()->insert('off_user_sign', $columns)->execute();
       // if(!$rs){
       //     $this->setError('签到失败!');
       //     return false;
       // }
       
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
}