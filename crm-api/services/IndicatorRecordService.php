<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
use app\models\UserLocation;
use app\models\Member;
use app\models\Supplier;
use app\models\Shop;
use app\benben\DateHelper;
use aPP\models\Indicator;
use app\models\IndicatorCompany;
use app\models\IndicatorRecord;
use app\models\CompanyCategroy;
use app\models\CompanyShopNote;
use app\services\UserRecordNewService;
//use app\benben\DateHelper;

class IndicatorRecordService extends Service
{
    /**
     * 业务指标记录查询
     * @param string $user_id 查询人
     * @param string $type 查询类型，1为个人，2为本组
     * @param string $num 查询指标
     * @return array
     */
    public function getIndicatorRecord($user_id,$type,$num)
    {
      if(!$user_id)
      {
          $this->setError('用户ID不能为空');
          return false;
      }
      $user_data = User::find()
                  ->select(["username","company_categroy_id","group_id"])
                  ->where(["id"=>$user_id])
                  ->one();
                  //return $user_data;
      if(!$user_data)
      {
          $this->setError('此人不存在!');
          return false;
      }
      if($type == "")
      {
          $type = 0;//默认为个人;
      }
      if(!$num)
      {
        $num = 1;
      }
      $company_id = $user_data['company_categroy_id'];//企业ID
      $group_id = $user_data['group_id'];
      $company_data = CompanyCategroy::find()
          ->select(["name","fly"])
          ->where(["id" => $company_id])
          ->asArray()
          ->one();
      /*云媒及其子公司调接口*/
      if($company_id == '1' || $company_data['fly'] == '1')
      {
        $service = UserRecordNewService::instance();
        $result = $service->getUserRecord($user_id,$type,$num);
      }
      /*外来公司调取本地数据*/
      else 
      {
        $result = $this->getLocalRecord($group_id,$user_id,$type,$num,$company_id);
      }
      if(!$result)
      {
        $this->setError("暂时没有指标数据");
        return false;
      }
      return $result;
    }


    /*调取本地数据*/
    public function getLocalRecord($group_id,$user_id,$type,$num,$company_id)
    {
      if($type == "0")//个人
      {
        $user = $user_id;
      }
      else//本组
      {
        if($group_id == "0")//没有分组
        {
          $user = $user_id;
        }
        else
        {
            $user = $this->getGroupUser($group_id,$company_id);
//          $user = join(",",$user_arr);
        }
      }
        $result = $this->getData($user,$num,$group_id);

        return $result;
    }

    /*获取个人或本组指标记录*/
    public function getRecordData($user,$num,$start,$end)
    {
        switch ($num){
            //1为拜访客户指标（包括新增和回访的）
            case 1:
                return $this->newShop($user,$num,$start,$end);
                break;
        }

    }

    /**
     * @param $user     用户ID
     * @param $num      指标参数
     * @param $start    开始时间
     * @param $end      结束时间
     * @return bool
     */
    public function newShop($user,$num,$start,$end){
        if(!$user){
            $this->setError("用户ID不能为空");
            return false;
        }
        //1为拜访客户指标
        if($num == 1){
            //新增记录
            $shop_data = Shop::find()
                ->select(['id','shop_name','user_id','user_name','createtime'])
                ->where(['in','user_id',$user])
                ->andWhere(['between','createtime',$start,$end])
                ->asArray()
                ->all();

            $user_name = User::find()
                ->select(['id','username'])
                ->where(['in','id',$user])
                ->asArray()
                ->all();

            foreach($user_name as $key=>$v){
                $name[$key] = $v['username'];
            }

            //回访记录
            $visit_data = CompanyShopNote::find()
                ->select(['id','user','time','note'])
                ->where(['in','user',$name])
                ->andWhere(['between','time',$start,$end])
                ->asArray()
                ->all();
            $day_result = count($shop_data)+count($visit_data);
            return $day_result;
        }

    }

    /*获取本组所有人*/
    public function getGroupUser($group_id,$company_id)
    {
        if(!$group_id)
        {
          $this->setError("分组ID不能为空");
          return false;
        }
        if(!$company_id)
        {
            $this->setError("公司ID不能为空");
            return false;
        }
        $group_user = User::find()
                    ->select(["id","username"])
                    ->where(["group_id"=>$group_id])
                    ->andwhere(["company_categroy_id"=>$company_id])
                    ->asArray()
                    ->all();
        for($i=0;$i<count($group_user);$i++)
        {
          $data = $group_user[$i]['id'];
          $result[] = $data;
        }
        return $result;
    }

      /*将数据转变为需要的格式*/
     public function getData($user,$num,$group_id)
    {  
       //  //今天开始与结束时间
        $start = DateHelper::getTodayStartTime();
        $end = DateHelper::getTodayEndTime();
       //  //昨天的开始时间
        $yestoday = DateHelper::getYesterdayStartTime();
        //本周的开始时间
        $weekStart = DateHelper::getWeekStartTime(0);
        //上周的开始时间
        $preStart = DateHelper::getWeekStartTime(1);
        //本月的开始时间
        $monthStart = strtotime(date("Y-m-01",strtotime(date('Y-m',time()))));
        //获取上个月的开始时间
        $preMonthStart = DateHelper::getPreMonthStartTime();
       // echo $preMonthStart;echo '+++++';echo $preStart;exit();
        //本日、本周、本月统计数据
        $res = array();
        /* 今日   本周    本月 */
            $visitNum = $this->getRecordData($user,$num,$start,$end,$group_id);
            $visitWeekNum = $this->getRecordData($user,$num,$weekStart,$end,$group_id);
            $visitMonthNum = $this->getRecordData($user,$num,$monthStart,$end,$group_id);
            $visitSum = $this->getRecordData($user,$num,0,$end,$group_id);
            /* 昨日   上周   上月 */
            $yestodayVisitNum = $this->getRecordData($user,$num,$yestoday,$start-1,$group_id);
            $preVisitWeekNum = $this->getRecordData($user,$num,$preStart,$weekStart,$group_id);
            $preVisitMonthNum = $this->getRecordData($user,$num,$preMonthStart,$monthStart,$group_id);
            $res['Record'] = [
                'day'=>(float)$visitNum,
                'week'=>(float)$visitWeekNum,
                'month'=>(float)$visitMonthNum,
                'sum'=>(float)$visitSum,
                'yesterday'=>(float)$yestodayVisitNum,
                'lastweek'=>(float)$preVisitWeekNum,
                'lastmonth'=>(float)$preVisitMonthNum
            ];
             
            $res['Increase'] = [
                'day'=>(float)$visitNum-(float)$yestodayVisitNum,
                'week'=>(float)$visitWeekNum-(float)$preVisitWeekNum,
                'month'=>(float)$visitMonthNum-(float)$preVisitMonthNum
            ];
       return  $res; 
    }
    
}