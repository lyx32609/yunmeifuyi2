<?php
/**
 * Created by 付腊梅
 * User: Administrator
 * Date: 2017/3/3 0003
 * Time: 下午 4:53
 */
namespace app\services;
use app\foundation\Service;
use app\models\User;
use app\models\ShopNote;
use app\models\UserGroup;
use app\models\UserBusinessNotes;
use app\models\Regions;
//use app\services\GetPerBusinessRankService;
use Yii;
class GetPerBusinessRankService extends Service
{
    /**
     *  获取个人业务排名
     * @param  [type] $username 用户名
     * @param  [type] $timeType 时间类型
     * @return [type]  array
     */
    private $arr = ['拜访客户','累计注册量','累计自己注册','累计订单数量','累计订单金额','累计订单用户数量','累计预存款订金额','累计买买金订单量','累计买买金订单金额 ','累计买买金订单用户量'];
    public function getPerRank($username,$type)
    {

        if(!$username)
        {
            $this->setError('用户名不能为空');
            return false;
        }
        if(!$type)
        {
            $this->setError('指标不能为空');
            return false;
        }
        $department = User::find()
                    ->select(["department_id"])
                    ->where(["username"=>$username])
                    ->one();
        $department_id = $department['department_id'];
        $timeType = [1,2,3];
        //本日
        $Dstime = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $Detime = mktime(23,59,59,date('m'),date('d'),date('Y'));
        //本周
        $Wstime = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'));
        $Wetime = mktime(23,59,59,date('m'),date('d')-date('w')+6,date('Y'));
        //本月
        $Mstime = mktime(0,0,0,date('m'),1,date('Y'));
        $Metime = mktime(23,59,59,date('m'),date('t'),date('Y')); 
        $userdata = User::find()
                ->select(["username","id"])
                ->where(["department_id"=>$department_id])
                ->asArray()
                ->all();
                    //本日数据
                    foreach($userdata as $k=>$val)
                    {
                            $data1 = $this->getParamet($type,$val['username'],$Dstime,$Detime);
                            $data[$k]['num'] = $data1['num'];
                            $data[$k]['typeName'] = $data1['typeName'];
                            $data[$k]['username'] = $val['username'];  
                    }
                    $day_result = $this->my_sort($data,'num',SORT_DESC);
                    foreach($day_result as $k=>$v)
                    {
                        if($v['username'] == $username)
                        {
                            $day_data['rank'] = $k+1;
                            $day_data['num'] = $v['num'];
                            $day_data['typeName'] = $v['typeName'];
                        }
                    }

                    //本周数据
                    foreach($userdata as $k=>$val)
                    {
                            $data1 = $this->getParamet($type,$val['username'],$Wstime,$Wetime);
                            $data[$k]['num'] = $data1['num'];
                            $data[$k]['typeName'] = $data1['typeName'];
                            $data[$k]['username'] = $val['username'];  
                    }
                    $week_result = $this->my_sort($data,'num',SORT_DESC);
                    foreach($week_result as $k=>$v)
                    {
                        if($v['username'] == $username)
                        {
                            $week_data['rank'] = $k+1;
                            $week_data['num'] = $v['num'];
                            $week_data['typeName'] = $v['typeName'];
                        }
                    }

                    //本月数据
                    foreach($userdata as $k=>$val)
                    {
                            $data1 = $this->getParamet($type,$val['username'],$Mstime,$Metime);
                            $data[$k]['num'] = $data1['num'];
                            $data[$k]['typeName'] = $data1['typeName'];
                            $data[$k]['username'] = $val['username'];  
                    }
                    $month_result = $this->my_sort($data,'num',SORT_DESC);
                    foreach($month_result as $k=>$v)
                    {
                        if($v['username'] == $username)
                        {
                            $month_data['rank'] = $k+1;
                            $month_data['num'] = $v['num'];
                            $month_data['typeName'] = $v['typeName'];
                        }
                    }
                    $return_data = array($day_data,$week_data,$month_data);
                    return $return_data;

    }
    
    
    
    /**
     *  获取接口所需数据
     * @param  $type 类型   1:拜访客户   2:累计注册量   3:累计自己注册   4:累计订单数量  5:累计订单金额  6:累计订单用户数量  7:累计预存款订金额  8:累计买买金订单量  9:累计买买金订单金额  10:累计买买金订单用户量
     * @param  $users 传入的用户  ['110','119','120']
     * @param  $stime 开始时间  时间戳
     * @param  $etime 结束时间 时间戳
     * @return [type]  array
     */
    public function getParamet($type,$users,$stime,$etime)
    {
        if(!$users){ //如果没有user值  返回0 数据为0
            return 0;
        }
        $useridarray = explode(',', $users);  //type = 1 需要传入数组结构
        $users ='['.$users.']'; // 其他需要传入 字符串结构
        switch ($type){
            case 1://拜访客户
                $data =  $this->visitData($useridarray,$stime,$etime);

                $result['num']= $data['visitNum'];
                $result['typeName'] = "拜访客户";
                break;
            case 2://累计注册量
                $paramet = [
                    'staff_id' => $users,
                    'start' => $stime,
                    'end' => $etime,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsMember',$paramet);
                $result['num'] = $data['result'];
                $result['typeName'] = $this->arr[1];
                break;
            case 3://累计自己注册
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsMember',$paramet);
                $result['num'] = $data['result'];
                $result['typeName'] = $this->arr[2];

                break;
            case 4://累计订单数量
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                'payment' => 0,
                'status' => "'finish'",
                'type' => 0,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                $result['num'] = $data['result']['num'];
                $result['typeName'] = $this->arr[3];
                break;
            case 5://累计订单金额
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                'payment' => 0,
                'status' => "'finish'",
                'type' => 0,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                $result['num'] = $data['result']['amount'];
                $result['typeName'] = $this->arr[4];
                break;
            case 6://累计订单用户
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                'payment' => 0,
                'status' => "'finish'",    
                'type' => 1,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                $result['num'] = count($data['result']);
                $result['typeName'] = $this->arr[5];
                break;
            case 7://累计预存款订金额
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                'payment' => 51,
                'status' => "'finish'",
                'type' => 0,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                $result['num'] = $data['result']['amount'];
                $result['typeName'] = $this->arr[6];
                break;
            case 8://累计买买金订单量
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                'payment' => 63,
                'status' => "'finish'",
                'type' => 0,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                $result['num'] = $data['result']['num'];
                $result['typeName'] = $this->arr[7];
                break;
            case 9://累计买买金订单金额
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                'payment' => 63,
                'status' => "'finish'",
                'type' => 0,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                $result['num'] = $data['result']['amount'];
                $result['typeName'] = $this->arr[8];
                break;
            case 10://累计买买金订单用户量
                $paramet = [
                'staff_id' => $users,
                'start' => $stime,
                'end' => $etime,
                'payment' => 63,
                'status' => "'finish'",
                'type' => 1,
                ];
                $data =  Yii::$app->api->request('statistics/statisticsOrder',$paramet);
                $result['num'] = count($data['result']);
                $result['typeName'] = $this->arr[9];
                break;
        }
        if(is_null($result['num'])){
            $result['num'] = 0;
        }
        return $result;
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
    
    
    //二维数组排序方法
    function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC  )
    {
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }
    
    
    
    

}