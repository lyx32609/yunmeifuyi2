<?php
namespace app\services;

use app\models\UserRealLocation; 
use app\foundation\Service;
use app\models\User;
use app\models\UserSign;

class UserRealLocationNewService extends Service
{
    /* 
     * 员工实时定位信息保存
     *  
     *  */
    public function userRealLocation($user_id,$longitude,$latitude)
    {
        if(!$longitude||!$latitude||!$user_id)
        {
            $this->setError('信息不可为空');
            return false;
        }
        $user=User::findOne($user_id);
        if(!$user)
        {
            $this->setError('用户不存在');
            return false;
        }
        $date=date("Y-m-d");
        $start=strtotime($date);
        $end=$start+24*3600;
        $sign_in=UserSign::find()->andWhere(['user'=>$user_id])->andWhere(['between','time',$start,$end])->one();
        
        if(!sign_in||empty($sign_in))
        {
            return [
                'ret'=>'10',
                'msg'=>'今日尚未打卡',
            ];
        }
        
        $data=UserRealLocation::find()->andWhere(['user'=>(string)$user_id])->orderBy('time desc')->one();
        if($_SERVER['REQUEST_TIME']-$data->time<840)
        {
            return [
                'ret'=>'30',
                'msg'=>'定位时间小于15分钟',
            ];
        }
        $model=new UserRealLocation();
        $model->user=(string)$user_id;
        $model->longitude=$longitude;
        $model->latitude=$latitude;
        $model->time=(int)time();
        $model->domain_id= $user->domain_id;
        if($model->save())
        {
            return [
                'msg'=>'成功'
            ];
        }
        else 
        {
            $this->setError('存储失败');
            return false;
        }
    }
    
    /* 
     * 根据时间获取当天的定位坐标
     *  
     *  */
    public function getRealLocation($user_id,$date)
    {
        if(!$date)
        {
            $date=date("Y-m-d");
        }
        $user=User::findOne($user_id);
        if(!$user)
        {
            $this->setError('用户不存在');
            return false;
        }
        if(strtotime($date)>time()||strtotime($date)<1464710400)
        {
          
            $this->setError('查询时间错误');
            return false;
        }
      
        $start=strtotime($date);
        $end=$start+24*3600;
        $records=UserRealLocation::find()->andwhere(['user'=>$user_id])->andwhere(['between','time',$start,$end])->orderBy('time desc')->all();
        $arr=[];
        foreach ($records as $val)
        {
            $data['longitude']=$val->longitude;
            $data['latitude']=$val->latitude;
            $data['time']=$val->time;
            $arr[]=$data;
        }
        return $arr;
    }
}