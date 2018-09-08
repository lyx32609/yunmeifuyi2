<?php
namespace app\services;
use Yii;
use app\models\Member;
use app\models\Supplier;
use app\foundation\Service;
use app\models\UserLog;    
use app\models\User;

class PositionService extends Service
{
    public function update($identity,$id, $longitude,$latitude)
    {
        if (!$identity)
        {
            $identity='member';
        }
        if($identity=='member')
        {
            $shop=Yii::$app->api->request('basic/getMember',['member_id'=>$id]);            
            if(!$shop||$shop['ret']!=0)
            {
                $this->setError('未能正确获取店铺信息');
                return false;
            }else{
                $shop=$shop[0];
            }
            if (!$longitude || !$latitude)
            {
                $this->setError('经纬度不能为空');
                return false;
            }
            if($shop['longitude']==$longitude && $shop['latitude']==$latitude)
            {
                $this->setError('请不要重复提交相同坐标');
                return false;
            }
            $oldlongitude=$shop['longitude'];
            $oldlatitude=$shop['latitude'];
            $result=Yii::$app->api->request('alter/alterCoordinate',[
                'identity'=>'Member',
                'id'=>$id,
                'longitude'=>$longitude,
                'latitude'=>$latitude,
            ]);
         
            if($result&&$result['ret']===0)
            {
                $log_title='修改'.$shop['shopname'].'的定位坐标';
                $array=[
                    'member_id'=>$id,
                    'old_position'=>'longitude:'.$oldlongitude.' ,latitude:'.$oldlatitude,
                    'new_position'=>'longitude:'.$longitude.' ,latitude:'.$latitude,
                ];
                $log_text=json_encode($array);
                if($this->addLog($log_title, $log_text)==false)
                {
                    $this->setError('日志添加失败');
                    return false;
                } 
            }else{
                $this->setError($result['msg']);
                return false;
            }
            
        }
        elseif($identity=='supplier')
        {
           
            $supplier=Yii::$app->api->request('basic/getSupplier',['supplierId'=>$id]);
            if(!$supplier||$supplier['ret']!=0)
            {
                $this->setError('未能正确获取店铺信息');
                return false;
            }else{
                $supplier=$supplier[0];
            }
            if (!$longitude || !$latitude)
            {
                $this->setError('经纬度不能为空');
                return false;
            }
            if($supplier['longitude']==$longitude && $supplier['latitude']==$latitude)
            {
                $this->setError('请不要重复提交相同坐标');
                return false;
            }
            $oldlongitude=$supplier['longitude'];
            $oldlatitude=$supplier['latitude'];
            
            $result=Yii::$app->api->request('alter/alterCoordinate',[
                'identity'=>'Supplier',
                'id'=>$id,
                'longitude'=>$longitude,
                'latitude'=>$latitude,
            ]);
            if($result&&$result['ret']===0)
            {
                $log_title='修改'.$supplier['company_name'].'的定位坐标';
                $array=[
                    'supplier_id'=>$id,
                    'old_position'=>'longitude:'.$oldlongitude.' ,latitude:'.$oldlatitude,
                    'new_position'=>'longitude:'.$longitude.' ,latitude:'.$latitude,
                ];
                $log_text=json_encode($array);
                if($this->addLog($log_title, $log_text)==false)
                {
                    $this->setError('日志添加失败');
                    return false;
                }
            }else{
                $this->setError($result['msg']);
                return false;
            }
        }
        
        return true;
    }
    /*
     * 添加相关操作日志
     *
     *  */
    private  function addLog($log_title,$log_text)
    {
        $userLog = new UserLog();
        $uid = \Yii::$app->user->id;
        $user = User::findOne($uid);
        $userLog->user_id = $uid;
        $userLog->type=1;
        $userLog->log_title=$log_title;
        $userLog->log_text=$log_text;
        $userLog->add_time = time();
     
        if(!$userLog->save())
        {
            $this->setError($userLog->errors);
            return false;
        }
    
        return true;
    }
   
}