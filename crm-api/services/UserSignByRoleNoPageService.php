<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserSign;
use yii\data\Pagination;
use app\models\UserDepartment;
use Yii;

class UserSignByRoleNoPageService extends Service
{
    /**
     *  获取员工签到记录
     * @param  [type] $user 登陆人ID
     * @param  [type] $startTime 开始时间
     * @param  [type] $endTime 结束时间
     * @param  [type] $uid 被查询人id 如查全部为0 经理查某个人 传这个人的ID 其余此处为空
     * @param  [type] $page 结束时间 页码（默认为1）
     *
     * @return [type]  array
     */
   public function getSignByRole($user, $timeType, $uid)
    {
         if(!$user) {      
             $this->setError('用户不能为空');
             return false;
         }
         if(!$timeType) {
             $this->setError('时间不能为空');
             return false;
         }
         if($timeType == '1')
        {
            $stime = mktime(0,0,0,date('m'),date('d'),date('Y'));
            $etime = mktime(23,59,59,date('m'),date('d'),date('Y'));
        }
        elseif($timeType == '2')
        {
            $stime = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
            $etime = mktime(23,59,59,date('m'),date('d')-date('w')+6,date('Y'));
        }
        elseif($timeType == '3')
        {
            $stime = mktime(0,0,0,date('m'),1,date('Y'));
            $etime = mktime(23,59,59,date('m'),date('t'),date('Y'));
        }
         $user_data = User::find()
         ->select(['domain_id','group_id','department_id','rank','id'])
         ->where(['id'=>$user])
         ->asArray()
         ->one();
         if($user_data['ret'] == 0)
         {
             $rank = $user_data['rank'];
         }
         switch($rank)
         {
             case 1://普通员工
                  $result = $this->getLoneSign($user, $startTime, $endTime);
                  break;
             case 4://部门经理
                 $department_id = $user_data['department_id'];
                  $result = $this->getDepartmentSign($department_id, $startTime, $endTime, $uid,"");
                  break;
             case 3://城市经理
                 $domain_id = $user_data['domain_id'];
                 $result = $this->getDepartmentSign("", $startTime, $endTime,"",$domain_id);
             case 30://总经理
                  $result = $this->getLoneSign($user, $startTime, $endTime, $page);
                  break;
         }
        if(!$result) {
            $this->setError('没有人员签到信息');
            return false;
        }
       return $result;
    }

    public function getLoneSign($user, $startTime, $endTime)//如果是员工 查询自己的签到记录
    {
        $result = UserSign::find()
        ->select(['type','time'])
        ->where('user = :user',[':user' => $user])
        ->andWhere(['between', 'time', $startTime, $endTime])
        ->asArray()
        ->orderBy('time desc')
        ->all();
        return $result;
    }
    public function getDepartmentSign($department_id, $startTime, $endTime, $uid,$domain_id)//部门经理或城市经理
    {
       if(($uid != 0) && !empty($uid))//具体查某个人
       {
           $result = UserSign::find()
               ->select(['type','time'])
                ->where('user = :user',[':user' => $uid])
               ->andWhere(['between', 'time', $startTime, $endTime])
               ->orderBy('time desc')
               ->all();;
       }
        elseif($uid == 0)//该部门下所有
        {
            $department_user =  User::find()//该部门所有人的id
                ->select(['id','name'])
                ->where(['department_id'=>$department_id])
                ->asArray()
                ->all();

                for($i=0;$i<count($department_user);$i++)
                {
                    $uid_arr[] = $department_user[$i]['id'];
                }
            $result =	UserSign::find()
                ->select([ 'off_user_sign.id as id' ,'off_user_sign.type as type','off_user_sign.time as time','off_user.name as user_name'])
                ->from('off_user_sign')
                ->where(['in','off_user_sign.user',  $uid_arr])
                ->leftJoin('off_user', 'off_user_sign.user = off_user.id')
                ->asArray()
                ->all();
        }
        if($domain_id)//城市经理
        {
                $department_user =  User::find()//该部门所有人的id
                ->select(['id','name'])
                ->where(['domain_id'=>$domain_id])
                ->asArray()
                ->all();

                for($i=0;$i<count($department_user);$i++)
                {
                    $uid_arr[] = $department_user[$i]['id'];
                }
                $result1 = UserSign::find()
                ->select([ 'off_user_sign.id as id' ,'off_user_sign.type as type','off_user_sign.time as time','off_user.name as user_name','off_user_department.name as department_name'])
                ->from('off_user_sign')
                ->where(['in','off_user_sign.user',  $uid_arr])
                ->leftJoin('off_user', ' off_user.id = off_user_sign.user')
                ->leftJoin('off_user_department','off_user.department_id = off_user_department.id')
                ->orderBy('off_user_sign.time desc')
                ->asArray()
                ->all();

                foreach ($result1 as $key => $value) {
                    if($value['department_name'] == $value['department_name'])
                    {
                            
                        $arr[$value['department_name']]['department_name'] = $value['department_name'];
                        $arr[$value['department_name']]['sirn'][] = $value;
                    }
                }
                $result = array_values($arr);//消去key值
               return $result;
        }

       return $result;
    }
    

}