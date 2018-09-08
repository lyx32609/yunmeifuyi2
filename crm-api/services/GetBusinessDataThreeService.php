<?php
namespace app\services;

use app\foundation\Service;
use app\benben\DateHelper;
use app\models\User;
use app\models\UserDepartment;
use app\models\CompanyCategroy;
use app\models\Regions;
use app\models\IndicatorRecord;
use app\services\GetBusinessDataNumService;
use app\services\GetUserIdOrNameService;
class GetBusinessDataThreeService extends Service
{

	/*调取业务数据*/
	public function getData($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, $num)
	{
		//return $user_company_id;
		if($user_company_id == "")
		{
			$this->setError("登录人企业ID不能为空");
			return false;
		}
		if(!$area)
		{
			$this->setError("省份不能为空");
			return false;
		}
		if(!($department_name && $department_id))
		{
			$this->setError("部门不能为空");
			return false;
		}
		if(!$company_name)
		{
			$this->setError("公司名字不能为空");
			return false;
		}
		if(!$num)
		{
			$this->setError("指标不能为空");
			return false;
		}
	     $company_data = CompanyCategroy::find()
	          ->select(["name","fly"])
	          ->where(["id" => $user_company_id])
	          ->asArray()
	          ->one();
	         //return $company_data;
	      /*云媒及其子公司调接口*/   
	      if(($company_data['fly'] == 0) && ($company_data['name'] == "云媒股份") || ($company_data['fly'] == 1))
	      { 
	        $service = GetBusinessDataNumService::instance();
	        $result = $service->getBusinessData($area, $city, $department_name, $num);
	        //$result = "云媒";
	      }
	      /*外来公司调取本地数据*/
	      else
	      {
	        $result = $this->getLocalData($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, $num);
	       // $result = "未来";
	      }
	      return $result;

	}

	/*获取本地业务数据*/
	public function getLocalData($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, $num)
	{
		$service = GetUserIdOrNameService::instance();
		$user = $service->getuserDataByType($user_company_id, $area, $city, $department_name, $department_id, $company_name, $company_id, 1);
		if(!$user)
		{
			$this->setError("暂无人员数据");
			return false;
		}
		$result = $this->getDataByTime($user,$num);
		if(!$result)
		{
			$this->setError("暂无业务数据");
			return false;
		}
	}



	/*根据指标获取业务数据*/
	public function getBusinessByNum($user, $num, $start, $end)
	{
		$sql = "select r.*,i.indicator_name,SUM(num) as sum_num from off_indicator_record as r left join off_indicator as i on r.indicator_id = i.id where (user_id in (".$user.") and create_time >= ".$start ." and create_time <= ".$end." and indicator_id = '".$num."') group by user_id ";
      $data = IndicatorRecord::findBySql($sql)
            ->asArray()
            ->all();
        return $data[0];
	}

	/*根据时间类型获取业务数据*/
	public function getDataByTime($user,$num)
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
            $visitNum = $this->getBusinessByNum($user,$num,$start,$end);
            $visitWeekNum = $this->getBusinessByNum($user,$num,$weekStart,$end);
            $visitMonthNum = $this->getBusinessByNum($user,$num,$monthStart,$end);
            $visitSum = $this->getBusinessByNum($user,$num,0,$end);
            /* 昨日   上周   上月 */
            $yestodayVisitNum = $this->getBusinessByNum($user,$num,$yestoday,$start);
            $preVisitWeekNum = $this->getBusinessByNum($user,$num,$preStart,$weekStart);
            $preVisitMonthNum = $this->getBusinessByNum($user,$num,$preMonthStart,$monthStart);
            $res = [
                'day'=>(float)$visitNum['sum_num'],
                'week'=>(float)$visitWeekNum['sum_num'],
                'month'=>(float)$visitMonthNum['sum_num'],
                'yesterday'=>(float)$yestodayVisitNum['sum_num'],
                'lastweek'=>(float)$preVisitWeekNum['sum_num'],
                'lastmonth'=>(float)$preVisitMonthNum['sum_num'],
                'total'=>(float)$visitSum['sum_num']
            ];
       return  $res; 
	}
	
}