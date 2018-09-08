<?php
/**
 * Created by 付腊梅
 * User: Administrator
 * Date: 2017/3/3 0003
 * Time: 下午 4:53
 */
namespace app\services;
use app\foundation\Service;
use app\models\UserDepartment;
use yii\data\Pagination;
use app\models\User;
use app\models\GroupDepartment;
use app\models\UserDomain;
use app\models\ProviceCity;
use app\models\ShopNote;
use app\models\UserGroup;
use app\models\UserBusinessNotes;
use app\models\Regions;
use Yii;
class GetBusinessRankService extends Service
{
    /**
     *  获取员工签到记录
     * @param  [type] $area 省
     * @param  [type] $city 市
     * @param  [type] $department 部门
     * @param  $type 类型   1:拜访客户   2:累计注册量   3:累计自己注册   4:累计订单数量  5:累计订单金额  6:累计订单用户数量  7:累计预存款订金额  8:累计买买金订单量  9:累计买买金订单金额  10:累计买买金订单用户量
     * @param  $stime 开始时间  时间戳
     * @param  $etime 结束时间 时间戳
     * @param  [type] $page页码
     * @param  [type] $pageSize 每页显示多少条
     * @return [type]  array
     */
    private $arr = ['拜访客户','累计注册量','累计自己注册','累计订单数量','累计订单金额','累计订单用户数量','累计预存款订金额','累计买买金订单量','累计买买金订单金额 ','累计买买金订单用户量'];
    public function getBusinessRank($area,$city,$department,$stime,$etime,$type,$page,$pageSize,$group)
    {

        if(!$area)
        {
            $this->setError('省不能为空');
            return false;
        }
        if(!$city)
        {
            $city = '全部';
        }
        if(!$department)
        {
            $this->setError('部门不能为空');
            return false;
        }
        if(!$stime&$etime)
        {
            $this->setError('时间类型不能为空');
            return false;
        }
        if(!$type)
        {
            $this->setError('指标不能为空');
            return false;
        }
        if(!$page)
        {
            $page = 1;
        }
        if(!$pageSize)
        {
            $pageSize = 10;
        }
        if($area == '全国' && $city != '全部')
        {       
            $result = $this-> getAreaRank($department,$stime,$etime,$type);
        }
        elseif(($area != '全国') && ($city == '全部'))
        {
            $result = $this->getCityRank($area,$department,$stime,$etime,$type);
        }else//分组或个人排名
        {
            $result = $this->getGroupPersonRank($area,$city,$department,$group,$stime,$etime,$type,$page,$pageSize);
        }
        if(!$result)
        {
            $this->setError('暂时没有排名数据');
            return false;
        }
        return $result;

    }
    
    public function getAreaRank($department,$stime,$etime,$type)//全国
    {
        $arr = ['拜访客户','累计注册量','累计自己注册','累计订单数量','累计订单金额','累计订单用户数量','累计预存款订金额','累计买买金订单量','累计买买金订单金额 ','累计买买金订单用户量'];
        //拼接sql语句  查询出每个省下的员工编号 以省份分组（员工编号格式 ： 1，2，3，4）
        $sql = "SELECT a3.*,a4.aid,GROUP_CONCAT(a5.username) as userid from off_provice_city as a3 , 
                            (SELECT a1.domain_id,a2.id as aid from off_user_domain as a1 , 
                                (SELECT * from off_user_department where name = '".$department."') as a2  
                                        where a1.domain_id = a2.domain_id) as a4  
                                        LEFT JOIN off_user as a5 on a4.aid = a5.department_id  where a3.city_id = a4.domain_id GROUP BY a4.aid";
        $data = UserDepartment::findBySql($sql)
                ->andWhere(['is_show' => 1])
                ->asArray()
                ->all();
        $rsdata = array();
        foreach ($data as $k=>$v){
            $statistics = $this->getParamet($type,$v['userid'] , $stime, $etime);
            $rsdata[$v['province_id']]['name'] = $v['province_name'];
            $rsdata[$v['province_id']]['num'] += $statistics['num'];
            $rsdata[$v['province_id']]['typeName'] = $arr[$type-1];

        }
        //调用二维数组排序方法 
        $rsdata = $this->my_sort($rsdata,'num',SORT_DESC); 
        return $rsdata;
    }
    
    
    public function getCityRank($area,$department,$stime,$etime,$type)//市为全部///
    {
        
        //当地区没选中的这个部门时候  返回 部门不存在
        $sql = "SELECT * FROM off_user_domain where domain_id in 
                        (SELECT domain_id from off_user_department where name = '".$department."' ) and are_region_id in
                            (SELECT region_id from off_regions where p_region_id = 
                                (SELECT region_id FROM off_regions where local_name like '%".$area."'))";
        $areaData = UserDepartment::findBySql($sql)
                        ->andWhere(['is_show' => 1])
                        ->asArray()
                        ->all();
        if(count($areaData) == 0){
            $this->setError('该地区下没有此部门');
            return false;
        }
        
        $arr = ['拜访客户','累计注册量','累计自己注册','累计订单数量','累计订单金额','累计订单用户数量','累计预存款订金额','累计买买金订单量','累计买买金订单金额 ','累计买买金订单用户量'];
        //拼接sql语句  查询出每个市的员工编号 以省份分组（员工编号格式 ： 1，2，3，4）
        $sql = "SELECT a3.*,a4.aid,GROUP_CONCAT(a5.username) as userid from off_provice_city as a3 , 
                            (SELECT a1.are_region_id,a1.domain_id,a2.id as aid from off_user_domain as a1 , 
                                    (SELECT * from off_user_department where name = '".$department."') as a2  where a1.domain_id = a2.domain_id) as a4  
                                            LEFT JOIN off_user as a5 on a4.aid = a5.department_id  
                                            where a3.city_id = a4.domain_id  and a3.province_name = '".$area."' GROUP BY a4.domain_id";
        $data = UserDepartment::findBySql($sql)
                    ->andWhere(['is_show' => 1])
                    ->asArray()
                    ->all();
        $rsdata = array();
        foreach ($data as $k=>$v){
            $statistics = $this->getParamet($type,$v['userid'] , $stime, $etime);
            $rsdata[$v['city_id']]['name'] = $v['city_name'];
            $rsdata[$v['city_id']]['num'] += $statistics['num'];
            $rsdata[$v['city_id']]['typeName'] = $arr[$type-1];

        }
        //调用二维数组排序方法
        $rsdata = $this->my_sort($rsdata,'num',SORT_DESC);
        return $rsdata;
        
    }

    
    
    
    
    
    public function getGroupPersonRank($area,$city,$department,$group,$stime,$etime,$type,$page,$pageSize)//获取分组或个人排名
    {
        $arr = $this->arr;
        $province_data = Regions::find()->select ('region_id')->where(['like','local_name',$area])->one();//获取省的ID
        $province_id = $province_data['region_id'];
        $city_data = Regions::find()->select ('region_id')->where(['like','local_name',$city])->andWhere(['p_region_id'=>$province_id])->one();//获取市的ID
        $city_id = $city_data['region_id'];
        $domain = UserDomain::find()->select("domain_id")->where(['like','region',$city])->andWhere(['are_region_id' =>$city_id])->one();
        $domain_id = $domain['domain_id'];//获取区域的ID
        $department_data = UserDepartment::find()
            ->select(['id','name','domain_id'])
            ->where(['domain_id'=>$domain_id])
            ->andWhere(["name"=>$department])
            ->andWhere(['is_show' => 1])
            ->one();
        $department_id = $department_data['id'];
        $rows = UserGroup::find()
            ->select(["id","name"])
            ->where(["department_id"=>$department_id]);

        $pagination = new Pagination([
        'params'=>['page'=>$page],
        'defaultPageSize' => $pageSize,
        'totalCount' => $rows->count(),
            ]);//分页参数

        $group_data = $rows->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()->all();//分页查寻分组
                //return $group_data;
        if($group_data && (!$group))//有分组
        {
            foreach($group_data as $k=>$g_val)
            {
                $rows = User::find()
                ->select(["off_user_group.name,GROUP_CONCAT(off_user.username) as userId"])
                ->where(["off_user.group_id"=>$g_val['id']])
                ->leftJoin("off_user_group","off_user_group.id = off_user.group_id");
                
                $pagination = new Pagination([
                'params'=>['page'=>$page],
                'defaultPageSize' => $pageSize,
                'totalCount' => $rows->count(),
                    ]);//分页参数

                $user_data = $rows->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()->all();//分页查寻分组
                
                if($user_data[0]['userId'])
                {
                    $data_apiReturn = $this->getParamet($type,$user_data[0]['userId'],$stime,$etime);
                    $result[$k]['name'] = $g_val['name'];
                    $result[$k]['num'] = $data_apiReturn['num'];
                    $result[$k]['typeName'] = $arr[$type-1];
                }
                else
                {
                    $result[$k]['name'] = $g_val['name'];
                    $result[$k]['num'] = 0;
                    $result[$k]['typeName'] = $arr[$type-1];
                }

            }
            $result = $this->my_sort($result,'num',SORT_DESC);
            return $result;
        }
        elseif(!$group_data)//没有分组
        {
            if($group == "部门人员")
            {
                $rows = User::find()
                ->select(["id","username","name"])
                ->where(["department_id"=>$department_id]);
                $pagination = new Pagination([
                    'params'=>['page'=>$page],
                    'defaultPageSize' => $pageSize,
                    'totalCount' => $rows->count(),
                        ]);//分页参数

                $user_data = $rows->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->asArray()->all();//分页查寻分组
                if($user_data)
                {
                    foreach($user_data as $k=>$v)
                    {
                        $data_apiReturn = $this->getParamet($type,$v['username'],$stime,$etime);
                        $result[$k]['userid'] = $v['id'];
                        $result[$k]['username'] = $v['username'];
                        $result[$k]['name'] = $v['name'];
                        $result[$k]['num'] = $data_apiReturn['num'];
                        $result[$k]['typeName'] = $arr[$type-1];
                    }
                    $result = $this->my_sort($result,'num',SORT_DESC);
                }
                else
                {
                    $result = "";
                }                    
            }
            elseif($group == "")
            {
                $userdata = User::find()
                ->select(["GROUP_CONCAT(username) as userId"])
                ->where(["department_id"=>$department_id])
                ->asArray()
                ->all();
                if($userdata[0]["userId"])
                {
                    $data_apiReturn = $this->getParamet($type,$userdata[0]['userId'],$stime,$etime);
                    $result[0]['name'] = '部门人员';
                    $result[0]['num'] = $data_apiReturn['num'];
                    $result[0]['typeName'] = $arr[$type-1];
                }
                else
                {
                    $result = "";
                }
                return $result;
                
            }


            return $result;

        }

        if($group)//查分组下的人员
        {
            $rows = User::find()
            ->select(["off_user.id","off_user.username","off_user.name"])
            ->leftJoin('off_user_group', 'off_user_group.id = off_user.group_id')
            ->where(["off_user_group.name"=>$group]);
            $pagination = new Pagination([
                'params'=>['page'=>$page],
                'defaultPageSize' => $pageSize,
                'totalCount' => $rows->count(),
                    ]);//分页参数

            $user_data = $rows->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()->all();//分页查寻分组
            foreach($user_data as $k=>$v)
            {
                $data_apiReturn = $this->getParamet($type,$v['username'],$stime,$etime);
                $result[$k]['userid'] = $v['id'];
                $result[$k]['username'] = $v['username'];
                $result[$k]['name'] = $v['name'];
                $result[$k]['num'] = $data_apiReturn['num'];
                $result[$k]['typeName'] = $data_apiReturn['typeName'];
            } 
            $result = $this->my_sort($result,'num',SORT_DESC);
            return $result;
        }
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
                $result['typeName'] = $this->arr[0];
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