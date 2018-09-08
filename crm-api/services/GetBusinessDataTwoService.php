<?php
namespace app\services;

use app\foundation\Service;
use app\benben\DateHelper;
use app\models\User;
use app\models\UserDepartment;
use app\models\GroupDepartment;
use app\models\UserDomain;
use app\models\ProviceCity;
use app\models\ShopNote;
use app\models\UserGroup;
use app\models\UserBusinessNotes;
use app\models\CompanyCategroy;
use app\models\Regions;
use app\models\IndicatorRecord;
use app\services\GetBusinessDataNumService;
class GetBusinessDataTwoService extends Service
{

	/*调取业务数据*/
	public function getData($company_id, $area, $city, $department, $num)
	{
		if(!$company_id)
		{
			$this->setError("企业ID不能为空");
			return false;
		}
		if(!$area)
		{
			$this->setError("省份不能为空");
			return fasle;
		}
		if(!$department)
		{
			$this->setError("部门不能为空");
			return false;
		}
		if(!$num)
		{
			$this->setError("指标不能为空");
			return false;
		}
	      $company_data = CompanyCategroy::find()
	          ->select(["name","fly"])
	          ->where(["id" => $company_id])
	          ->asArray()
	          ->one();
	      /*云媒及其子公司调接口*/   
	      if(($company_data['fly'] == 0) && ($company_data['name'] == "云媒股份") || ($company_data['fly'] == 1))
	      {
	        $service = GetBusinessDataNumService::instance();
	        $result =$service->getBusinessData($area, $city, $department, $num);
	      }
	      /*外来公司调取本地数据*/
	      else
	      {
	        $result = $this->getLocalData($area, $city, $department, $num);
	      }
	      return $result;

	}

	/*获取本地业务数据*/
	public function getLocalData($area, $city, $department, $num)
	{
		if($area == "全国")
		{
			$department_all = UserDepartment::find()
							->select(["id","domain_id"])
							->where(["name" => $department])
							->andWhere(['is_show' => 1])
							->asArray()
							->all();
			if(!$department_all)
			{
				$this->setError("全国暂无此部门");
				return false;
			}
			if(count($department_all) > 1)
			{
				for($i=0;$i<count($department_all);$i++)
				{
					$list[$i] = $deepartment[$i]['id'];
				}
			}
			else
			{
				$list[0] = $department_all[0]['id'];
			}
			$department_user = $this->departmentUser($list);
			if(!$department_user)
			{
				$this->setError("该部门暂无人员");
			}
			$result = $this->getDataByTime($department_user,$num);
		}
		if($area != "全国")
		{
			if(($city == "全部") || ($city == "") )
			{
				$area_data = Regions::find()
						->select(['region_id'])
						->where(['local_name' => $area])
						->asArray()
						->one();
				$city_data = Regions::find()
						->select(['region_id'])
						->where(['p_region_id' => $area_data['region_id']])
						->asArray()
						->all();
				if(!$city_data){
					$this->setError('该省份暂无任何城市');
				}
				for($i = 0; $i < count($city_data); $i++){
					$domain_id[$i] = $city_data[$i]['region_id']; 
				}
				if(count($domain_id) > 1 ){
					$department_all = UserDepartment::find()
							->select(['id'])
							->where(['in', 'domain_id', $domain_id])
							->andWhere(['name' => $department])
							->andWhere(['is_show' => 1])
							->asArray()
							->all();
					if(!$department_all){
						$this->setError('该省份暂无相应部门');
					}
					for($i = 0; $i < count($department_all); $i++){
						$list[$i] = $department_all[$i]['id'];
					}
				} else {
					$department_all = UserDepartment::find()
							->select(['id'])
							->where(['in', 'domain_id', $domain_id])
							->andWhere(['name' => $department])
							->andWhere(['is_show' => 1])
							->asArray()
							->one();
					if(!$department_all){
						$this->setError('该省份暂无相应部门');
						return false;
					}
					$list[0] = $department_all['id'];
				}
				$department_user = $this->departmentUser($list);
				if(!$department_user){
					$this->setError('该部门暂无人员');
					return false;
				}
				$result = $this->getDataByTime($department_user,$num);
			}
			if($city != "全部")
			{
				$area_data = Regions::find()
						->select(['region_id'])
						->where(['local_name' => $area])
						->asArray()
						->one();
				$city_data = Regions::find()
						->select(['region_id'])
						->where(['p_region_id' => $area_data['region_id']])
						->andWhere(["local_name" => $city])
						->asArray()
						->all();
				$domain_id = $city_data["region_id"];
				if(!$domain_id){
					$this->setError('该城市暂未开通');
					return false;
				}
				$department_all = UserDepartment::find()
						->select(['id'])
						->where(['domain_id' => $domain_id])
						->andWhere(['name' => $department])
						->andWhere(['is_show' => 1])
						->asArray()
						->one();
				if(!$department_all){
					$this->setError('该城市暂无该部门');
					return false;
				}
				$list[0] = $department_all['id'];
				$department_user = $this->departmentUser($list);
				if(!$department_user){
					$this->setError('该部门暂无人员');
					return false;
				}
				$result = $this->getDataByTime($department_user,$num);
			}
		}
		if(!$result)
		{
			$this->setError("暂无业务数据");
			return false;
		}
		return $result;
	}

	/*获取该部门以及该搜索条件下所有人员*/
	public function departmentUser($department)
	{
		$domain = UserDepartment::find()
				->select(['domain_id'])
				->where(['in', 'id', $department])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
		for($i = 0; $i < count($domain); $i++){
			$domain_id[$i] = $domain[$i]['domain_id'];
		}
		$data = User::find()
				->select(['id'])
				->where(['in', 'department_id', $department])
				->andWhere(['in', 'domain_id', $domain_id])
				->asArray()
				->all();
		if(!$data){
			$this->setError('该部门暂无人员');
			return false;
		}
		for($i = 0; $i < count($data); $i++){
			$list[$i] = $data[$i]['id'];
		}
		if(count($list)>1)
		{
			$user = join(",",$list);
		}
		else
		{
			$user = $list[0];
		}
		return $user;
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