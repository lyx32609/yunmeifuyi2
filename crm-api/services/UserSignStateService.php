<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserSign;
use yii\db\Query;
use yii\data\Pagination;

class UserSignStateService extends Service
{
    /**
     * 查询签到情况
     * @param int $user 用户ID
     * @return array
     */
    public function getUserSignState($user)
    {
        if(!$user){
            $this->setError('用户不能为空');
            return false;
        }
        $data = $this->getUserSign($user);
        return $data;
    }
    
    /**
     * 查询签到情况
     * @param int $user 用户ID
     * @return array
     */
    public function getUserSignNew($user)
    {
       $start = $data = strtotime(date("Y-m-d"),time());
       $res = $this->getUserSignDetail($user, $start,time());
       return  count($res) % 2 ? '1' : '0';
    }
    /**
     * 查询签到情况
     * @param int $user 用户ID
     * @return array
     */
  	public function getUserSign($user)
    {
       
       $day_time=strtotime(date('Y-m-d', time()));
       $sign_time=strtotime(date('y-m-d',$res['time']));
       $res = $this->getUserSignDetail($user, $day_time, time());
       $time = $day_time-$sign_time;
       if ($time>=24*3600){
           return [msg=>'未签到',result=>$res];
       }
       elseif ($res['type']==1)
       {
           return [msg=>'未签退',result=>$res];
       }
       else  
       {
           return [msg=>'未签到',result=>$res];
       }
         
    }
    /**
     * 查询签到情况
     * @param int $user 用户ID
     * @return array 
     */
    public function getUserSignDetail($user, $start, $end)
    {
        $userSign = UserSign::find()
                    ->andWhere('user = :user',array(':user' =>$user))
                    ->andWhere(['source_type' => 1])
                    ->andWhere(['between', 'time', $start, $end])
                    ->asArray()
                    ->all(); 
        return $userSign;
       
    }

    /**
     *  获取所选员工的签到记录
     * @parm string  userid 用户id的字符串 用逗号间隔   page页码
     * @return array
     * @author qizhifei
     */
    public function getSelectedUserSign($userid, $page = 1)
    {
        if(! $userid){
            $this->setError('员工编号不能为空!');
            return false;
        }
        $data = explode(',', $userid);
        $query = UserSign::find()
                    ->select(['off_user_sign.id as id','off_user_sign.user as user','off_user_sign.type as type','off_user_sign.time as time','off_user.name as name'])
                    ->from('off_user_sign')
                    ->where(['off_user_sign.user'=>$data])
                    ->leftJoin('off_user', 'off_user_sign.user = off_user.id');
        //进行分页查询
        $pagination = new Pagination(['params'=>['page'=>$page],'defaultPageSize'=>20,'totalCount'=>$query->count()]);
        $userSign = $query
                    ->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->asArray()
                    ->orderBy('time desc')
                    ->all();
        if(! $userSign){
            $this->setError('当前人员没有签到信息信息');
            return false;
        }
        return $userSign;
    }
    
    
    
     
}