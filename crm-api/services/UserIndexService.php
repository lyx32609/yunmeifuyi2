<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\UserBusinessNotes;
use app\models\UserIndex;
use app\models\ShopNote;
use Yii;
use app\models\User;
class UserIndexService extends Service
{
    /**
     *   计划任务  统计昨日的员工指令
     */
    public function index()
    {
        $rs['stime'] = time();
        $startdata = $_POST['startdata'];
        //没有提交过来的数据 默认执行昨天   否则执行填写日期到今天
        if(isset($startdata) && $startdata != 0){
            //echo $startdata;exit();
            $second1 = strtotime($startdata);  
            $second2 =  strtotime(date("Y-m-d")); 
            if ($second1 < $second2) {    
                $tmp = $second2;    $second2 = $second1;    $second1 = $tmp;  
            } 
            $days =  ($second1 - $second2) / 86400;
            //echo $days;exit();
            $s1 = strtotime($startdata);
            for ($i = 0;$i < $days;$i++){
                $shijiancuo = $s1 + $i * 86400;
                $s2 = date('Y-m-d',$shijiancuo);
               // echo date('Y-m-d',$shijiancuo);exit();
                $stime = strtotime("$s2 00:00:00");
                $etime = strtotime("$s2 23:59:59");
                $this->synchronization($stime,$etime);
            }
        }else{
            //昨日的开始时间与结束时间戳
            $stime = strtotime('yesterday 00:00:00');
            $etime = strtotime('yesterday 23:59:59');        
            $this->synchronization($stime,$etime);
        }
        
        $rs['etime'] = time();
        return ['msg'=>$rs];
        
    }
    
    
    
    public function synchronization($stime,$etime){
        //获取到有效的用户    被统计的部门下所有的员工即为有效
        $subQuery =  (new \yii\db\Query())->select ( 'id')->from(UserDepartment::tableName())->where(['is_select'=>1])->andWhere(['is_show' => 1]);
        $userData = User::find()
        // ->select(' GROUP_CONCAT(username) as usernames ')
        ->select(' username')
        ->where(['department_id'=>$subQuery])
        ->asArray()
        ->all();
       // print_r($userData);exit();
     //   $commandQuery = clone $userData;
      //  echo $commandQuery->createCommand()->getRawSql();
     //   exit();
        if(!count($userData)){ //如果不存在则结束执行
            return 0;
        }else{
            $userIndeData = new UserIndex;
            foreach ($userData as $v){
                $userid = $v['username'];
                $useridarray = explode(',', $userid);  //指标1 需要传入数组结构
                $users ='['.$userid.']'; // 其他需要传入 字符串结构
                $userIndeDatas = $userIndeData::find()->where(['userid' => $userid,'inputtime' => $stime ])->count();
                if($userIndeDatas){
                    continue;
                }else{
                    //拜访客户
                    $data =  $this->visitData($useridarray,$stime,$etime);
                    $result['visitingnum']= $data['visitNum'] ?$data['visitNum']: 0;
                    //累计注册量  , 累计自己注册
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsMember',$paramet);
                    $result['registernum'] = $data['result'] ?$data['result']: 0;
        
                    //累计订单数量
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                        'payment' => 0,
                        'status' => "'finish'",
                        'type' => 0,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                    $result['ordernum'] = $data['result']['num'] ?$data['result']['num']: 0;
                    //累计订单金额
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                        'payment' => 0,
                        'status' => "'finish'",
                        'type' => 0,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                    $result['orderamount'] = $data['result']['amount'] ?$data['result']['amount']: 0;
                    //累计订单用户
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                        'payment' => 0,
                        'status' => "'finish'",
                        'type' => 1,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                    $result['orderuser'] = count($data['result']) ?count($data['result']): 0;
                    //累计预存款订金额
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                        'payment' => 51,
                        'status' => "'finish'",
                        'type' => 0,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                    $result['deposit'] = $data['result']['amount'] ?$data['result']['amount']: 0;
                    //累计买买金订单量
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                        'payment' => 63,
                        'status' => "'finish'",
                        'type' => 0,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                    $result['maimaijinorder'] = $data['result']['num']  ? $data['result']['num']: 0;
                    //累计买买金订单金额
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                        'payment' => 63,
                        'status' => "'finish'",
                        'type' => 0,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                    $result['maimaijinamount'] = $data['result']['amount']  ?$data['result']['amount']: 0;
                    //累计买买金订单用户量
                    $paramet = [
                        'staff_id' => $users,
                        'start' => $stime,
                        'end' => $etime,
                        'payment' => 63,
                        'status' => "'finish'",
                        'type' => 1,
                    ];
                    $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                    $result['maimaijinuser'] = count($data['result']) ?count($data['result']): 0;
                    
/*                     if($result['registernum'] == 0 && $result['maimaijinamount'] == 0 && $result['maimaijinorder'] == 0 && $result['deposit'] == 0 && $result['orderuser'] == 0 
                        && $result['orderamount'] == 0 && $result['ordernum'] == 0 && $result['registernum'] == 0 && $result['visitingnum'] == 0){
                        continue;
                    } */
                    
                    $userIndex = new UserIndex;
                    $userIndex->userid =$userid;
                    $userIndex->inputtime =$stime;
                    $userIndex->maimaijinamount =$result['maimaijinamount'];
                    $userIndex->maimaijinorder =$result['maimaijinorder'];
                    $userIndex->deposit =$result['deposit'];
                    $userIndex->orderuser =$result['orderuser'];
                    $userIndex->orderamount =$result['orderamount'];
                    $userIndex->ordernum =$result['ordernum'];
                    $userIndex->registernum =$result['registernum'];
                    $userIndex->visitingnum =$result['visitingnum'];
                    $userIndex->save();
                }
            }
        }
    }
    
    
    
    
    
    
    
    

    /**
     * 业务记录拜访商家查询
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    
    private function visitData($user,$start,$end)
    {
        /*         $start = mktime(0,0,0,date('m'),date('d'),date('Y'));
         $end = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1; */
        if(is_array($user)){
            $user = implode(',', $user);
        }
    
        $domainId = \Yii::$app->user->identity->domainId;
        $rows = (new \yii\db\Query())
        ->select('count(id) as visitNum')
        ->from(ShopNote::tableName())
        ->andWhere('user in ("'.$user.'")')
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->one(\Yii::$app->dbofficial);
    
        $nums = UserBusinessNotes::find()
        ->andWhere('staff_num in ("'.$user.'")')
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->count();
    
        $row = array();
        $row['visitNum'] = $rows['visitNum']+$nums;
        return $row;
    }
    
    
    
    
    
    
    
   
}