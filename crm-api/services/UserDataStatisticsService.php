<?php
namespace app\services;

use Yii;
use app\foundation\Service;
use app\benben\DateHelper;
use app\models\User;
use app\models\ShopNote;
use app\models\Payments;
use app\models\UserBusinessNotes;
class UserDataStatisticsService extends Service
{
    /**
     * 业务记录查询 
     * @param string $user 查询人
     * @param string $type 查询类型：0.查询本人 1.查询本组 2.查询部门
     * @return array
     */
    public function getStaffRecord($user,$type,$num,$payment=100)
    {
       
        $people = User::find()
        ->andWhere('id = :user',[':user'=>$user])
        ->one();
        if(!$people)
        {
            $this->setError('此人不存在!');
            return false;
        }

        if($type=='0'){
            if($num==5)
            {
                $result = $this->getData($people['username'],$num,$payment);
            }else{
                $ret=\Yii::$app->api->request('statistics/statisticsData',[
                    'users'=>json_encode($people['username']),
                    'num'=>$num,
                    'payment'=>$payment,
                ]);
                if($ret['ret']==0)
                {
                    $result=$ret['result'];
                }else{
                    $this->setError('接口查询数据获取失败');return false;
                }
            }
                   
            return  $result;
        }elseif ($type=='1'){
            $users =  $this->getUsers($user);
            if($num==5)
            {
                $result = $this->getData($users,$num,$payment);
            }else{
                $ret=\Yii::$app->api->request('statistics/statisticsData',[
                    'users'=>json_encode($users),
                    'num'=>$num,
                    'payment'=>$payment,
                ]);
                if($ret['ret']==0)
                {
                    $result=$ret['result'];
                }else{
                    $this->setError('接口查询数据获取失败');return false;
                }
            }
            
            return  $result;
        }elseif ($type=='2'){
            $users =  $this->getAllUsers($user);
            if(!$users||empty($users))
            {
                $this->setError('地区同部门没有人员需要统计');
                return false;
            }
            if($num==5)
            {
                $result = $this->getData($users,$num,$payment);
            }else{
                $ret=\Yii::$app->api->request('statistics/statisticsData',[
                    'users'=>json_encode($users),
                    'num'=>$num,
                    'payment'=>$payment,
                ]);
                if($ret['ret']==0)
                {
                    $result=$ret['result'];
                }else{
                    $this->setError('接口查询数据获取失败');return false;
                }
            }         
            return  $result;
        }else {
            $this->setError('查询类型不正确!');
            return false;
        }
        
    }
    /**
     * 查询本组的人
     * @param int $user 查询人
     * @return array
     */
    private function getUsers($user)
    {
        $gid = (new \yii\db\Query())
        ->select('group_id')
        ->from(User::tableName())
        ->andWhere('id = :user',[':user'=>$user])
        ->scalar(\Yii::$app->dbofficial);
        if($gid==0){
            /* 如果没有分组，返回人员所在地区需要查询的所有人 */
            $domainId = \Yii::$app->user->identity->domainId;
            $users = User::find()->select('username')
            ->andWhere('domain_id = :domainId',[':domainId'=>$domainId])
            ->andWhere('is_select=1')
            ->column();
        }
        else
        {
            $users = (new \yii\db\Query())
            ->select('username')
            ->from(User::tableName())
            ->andWhere('group_id = :gid', [':gid'=>$gid] )
            ->column(\Yii::$app->dbofficial);
        }
        return $users;
    }
    /**
     * 查询本部门的人
     * @param int $user 查询人
     * @return array
     */
    private function getAllUsers($user)
    {
        $domainId = \Yii::$app->user->identity->domainId;
        $dpmid = (new \yii\db\Query())
        ->select('department_id')
        ->from(User::tableName())
        ->andWhere('id = :user', [':user'=>$user] )
        ->andWhere('domain_id = :domainId',[':domainId'=>$domainId])
        ->scalar(\Yii::$app->dbofficial);
        
        if ($dpmid==0)
        {
            $users = User::find()->select('username')
            ->andWhere('domain_id = :domainId',[':domainId'=>$domainId])
            ->andWhere('is_select=1')
            ->column();
        }
        else
        {
            $users = User::find()->select('username')
            ->andWhere('department_id = :dpmid',[':dpmid'=>$dpmid])
            ->andWhere('domain_id = :domainId',[':domainId'=>$domainId])
            ->andWhere('is_select=1') 
            ->column();
        }
        return $users;
    }
    
    /**
     * 获取分组业务记录数据
     * @return array
     */
    public function getGroupRecord($groupId,$num,$payment=100)
    {
        
        if(!$groupId)
        {
            $this->setError('暂无此分组!');
            return false;
        }
        $users = $this->listStaff($groupId);
        if(!$users){
            $this->setError('分组人员获取失败');
            return false;
        }
        if($num==5)
        {
            $result = $this->getData($users,$num,$payment);
        }else{
            $ret=\Yii::$app->api->request('statistics/statisticsData',[
                'users'=>json_encode($users),
                'num'=>$num,
                'payment'=>$payment,
            ]);
            if($ret['ret']==0)
            {
                $result=$ret['result'];
            }else{
                $this->setError('接口查询数据获取失败');return false;
            }
        }

        if(!$result)
        {
            $this->setError('暂无此分组业务数据!');
            return false;
        }
        return  $result;
    }
    /**
     * 查询各个组成员
     * @param int $user 查询人
     * @return array
     */
    private function listStaff($gid)
    {
        $users = User::find()->select('username')
        ->where('group_id = :gid',[':gid'=>$gid])
        ->column();
        return $users;
    }
    
    
    ////////////////////////////////////////////////////
    /**
     * 业务记录组合数据
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    private function getData($user,$num,$payment)
    {
        
        //今天开始与结束时间
        $start = DateHelper::getTodayStartTime();
        $end = DateHelper::getTodayEndTime();
        //昨天的开始时间
        $yestoday = DateHelper::getYesterdayStartTime();
        //本周的开始时间
        $weekStart = DateHelper::getWeekStartTime(0);
        //上周的开始时间
        $preStart = DateHelper::getWeekStartTime(1);
        //本月的开始时间
        $monthStart = strtotime(date("Y-m-01",strtotime(date('Y-m',time()))));
        //获取上个月的开始时间
        $preMonthStart = DateHelper::getPreMonthStartTime();
        //本日、本周、本月统计数据
        $res = array();
       
        
        if($num==5)
        {
            /* 5、 拜访统计  */
            /* 今日   本周    本月 */
            $visitNum = $this->visitData($user,$start,$end);
            $visitWeekNum = $this->visitData($user,$weekStart,$end);
            $visitMonthNum = $this->visitData($user,$monthStart,$end);
            $visitSum = $this->visitData($user,0,$end);
            /* 昨日   上周   上月 */
            $yestodayVisitNum = $this->visitData($user,$yestoday,$start);
            $preVisitWeekNum = $this->visitData($user,$preStart,$weekStart);
            $preVisitMonthNum = $this->visitData($user,$preMonthStart,$monthStart);
            $res['VisitUser'] = [
                'day'=>(float)$visitNum['visitNum'],
                'week'=>(float)$visitWeekNum['visitNum'],
                'month'=>(float)$visitMonthNum['visitNum'],
                'sum'=>(float)$visitSum['visitNum'],
                'yesterday'=>(float)$yestodayVisitNum['visitNum'],
                'lastweek'=>(float)$preVisitWeekNum['visitNum'],
                'lastmonth'=>(float)$preVisitMonthNum['visitNum']
            ];
             
            $res['VisitUserIncrease'] = [
                'day'=>(float)$visitNum['visitNum']-(float)$yestodayVisitNum['visitNum'],
                'week'=>(float)$visitWeekNum['visitNum']-(float)$preVisitWeekNum['visitNum'],
                'month'=>(float)$visitMonthNum['visitNum']-(float)$preVisitMonthNum['visitNum']
                 
            ];
        }
      
        return $res;
    }
  
    /**
     * 业务记录拜访商家查询
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    private function visitData($user,$start,$end)
    {
        if(is_array($user)){
            $user = implode(',', $user);
        }
        $domainId = \Yii::$app->user->identity->domainId;
        $rows = (new \yii\db\Query())
        ->select('count(id) as visitNum')
        ->from(ShopNote::tableName())
        ->andWhere('user in ('.$user.')')
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->one(\Yii::$app->dbofficial);
    
        $nums = UserBusinessNotes::find()
        ->andWhere('staff_num in ('.$user.')')
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->count();
        $row = array();
        $row['visitNum'] = $rows['visitNum']+$nums;
        return $row;
    }

 

}