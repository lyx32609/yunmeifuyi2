<?php

namespace app\services;

use app\foundation\Service;
use app\benben\DateHelper;
use app\models\User;
use app\models\UserDepartment;
use app\models\GroupDepartment;
use app\models\UserDomain;
use app\models\ShopNote;
use app\models\UserGroup;
use app\models\UserBusinessNotes;
use yii\validators\SafeValidator;
use app\models\CompanyCategroy;
use app\models\CompanyBusinessNotes;
use app\models\Shop;
use app\models\Goods;
class GetBusinessDataFourService extends Service
{
    private $api = 'statistics/statisticsMember'; // 注册用户相关数据
    private $order_api = 'statistics/statisticsOrder'; //订单相关数据
    /**
     * 业务数据（改版后）
     * @param unknown $company_categroy_id
     * @param unknown $area_id
     * @param unknown $department_id
     * @param unknown $city_id
     * @param unknown $is_cooperation
     * @param unknown $num
     * @param unknown $user_id
     * @return boolean
     */
    public function getData($company_categroy_id, $area_id, $department_id, $city_id, $is_cooperation, $num, $user_id, $department_name)
    {
        if(!$company_categroy_id){
            $this->setError('公司id不能为空');
            return false;
        }
        if(!$area_id){
            $this->setError('省份id不能为空');
            return false;
        }
        if(!$department_id){
            $this->setError('部门id不能为空');
            return false;
        }
        if(!$city_id){
            $this->setError('城市id不能为空');
            return false;
        }
        if(!$user_id){
            $this->setError('用户id不能为空');
            return false;
        }
        $user = User::findOne(['id' => $user_id]);
        if(!$user){
            $this->setError('用户不存在');
            return false;
        }
        $result = $this->getCompanyData($company_categroy_id, $area_id, $department_id, $city_id, $num, $user->company_categroy_id, $is_cooperation, $department_name);
        return $result;
    }
    /**
     * 获取注册企业数据
     * @param unknown $company_categroy_id
     * @param unknown $area_id
     * @param unknown $department_id
     * @param unknown $city_id
     * @param unknown $num
     * @param unknown $user_id
     */
    private function getCompanyData($company_categroy_id, $area_id, $department_id, $city_id, $num, $user_company_id, $is_cooperation, $department_name)
    {
        if($area_id == -1){//如果省份选择全国
            $company_id = CompanyCategroy::find()
                        ->select(['id', 'fly'])
                        ->where(['id' => $user_company_id])
                        ->asArray()
                        ->one();
            if(!$company_id){
                $this->setError('公司不存在');
                return false;
            }
            $company_list = $this->getCompanyFly($company_id['id'], $company_id['fly'], $department_name);
            if(!$company_list){
                $this->setError('暂无用户信息');
                return false;
            }
            $list = $this->checkNum($num, $company_list, $is_cooperation);
        } else {//如果省份不选择全国
            if($city_id == -2){//如果城市选择全部
                $company_fly = CompanyCategroy::find()
                        ->select(['id', 'fly', 'area_id'])
                        ->where(['id' => $user_company_id])
                        ->asArray()
                        ->one();
                if(!$company_fly){
                     $this->setError('公司不存在');
                    return false;
                }
                if($company_fly['area_id'] != $area_id){
                    $company_id = CompanyCategroy::find()
                            ->select(['id', 'fly', 'area_id'])
                            ->where(['fly' => $user_company_id])
                            ->andWhere(['area_id' => $area_id])
                            ->asArray()
                            ->one();
                    if(!$company_id){
                        $this->setError('公司不存在');
                        return false;
                    }
                    for($i = 0; $i < count($company_id); $i++){
                        $company_num[$i] = $company_id[$i]['id'];
                    }
                } else {
                    $company_id = CompanyCategroy::find()
                            ->select(['id', 'fly', 'area_id'])
                            ->where(['fly' => $user_company_id])
                            ->andWhere(['area_id' => $area_id])
                            ->asArray()
                            ->all();
                    if($company_id){
                        for($i = 0; $i < count($company_id); $i++){
                            $company_num[$i] = $company_id[$i]['id'];
                        }
                        array_unshift($company_num, $user_company_id);
                    } else {
                        $company_num = [$user_company_id];
                    }
                    
                }
                $department_id = UserDepartment::find()
                        ->select(['id'])
                        ->where(['name' => $department_name])
                        ->andWhere(['in', 'company', $company_num])
                        ->andWhere(['is_show' => 1])
                        ->asArray()
                        ->all();
                if(!$department_id){
                    $this->setError('暂无部门信息');
                    return false;
                }
                for($i = 0; $i < count($department_id); $i++){
                    $department_list[$i] = $department_id[$i]['id'];
                }
                $company_user = $this->getCompanyUser($company_num, $department_list);
                if(!$company_user){
                    $this->setError('暂无用户信息');
                    return false;
                }
                $list = $this->checkNum($num, $company_user, $is_cooperation);
            } else {//如果城市不选择全部
                if($company_categroy_id == -3){//如果公司选择全部
                    $company_fly = CompanyCategroy::find()
                            ->select(['id', 'fly', 'area_id', 'domain_id'])
                            ->where(['id' => $user_company_id])
                            ->asArray()
                            ->one(); 
                    if(!$company_fly){
                        $this->setError('公司不存在');
                        return false;
                    }
                    if($company_fly['domain_id'] != $city_id){ //如果主公司不在该城市
                        $company_id = CompanyCategroy::find()
                                ->select(['id', 'fly', 'area_id'])
                                ->where(['fly' => $user_company_id])
                                ->andWhere(['domain_id' => $city_id])
                                ->asArray()
                                ->all();
                        if(!$company_id){
                            $this->sete('公司不存在');
                            return false;
                        }
                        for($i = 0; $i < count($company_id); $i++){
                            $company_num[$i] = $company_id[$i]['id'];
                        }
                    } else {
                        $company_id = CompanyCategroy::find()
                                ->select(['id', 'fly', 'area_id'])
                                ->where(['fly' => $user_company_id])
                                ->andWhere(['domain_id' => $city_id])
                                ->asArray()
                                ->all();
                        if($company_id){
                            for($i = 0; $i < count($company_id); $i++){
                                $company_num[$i] = $company_id[$i]['id'];
                            }
                            array_unshift($company_num, $user_company_id);
                        } else {
                            $company_num = [$user_company_id];
                        }
                    }
                    $department_id = UserDepartment::find()
                            ->select(['id'])
                            ->where(['name' => $department_name])
                            ->andWhere(['in', 'company', $company_num])
                            ->andWhere(['is_show' => 1])
                            ->asArray()
                            ->all();
                    if(!$department_id){
                        $this->setError('暂无部门信息');
                        return false;
                    }
                    for($i = 0; $i < count($department_id); $i++){
                        $department_list[$i] = $department_id[$i]['id'];
                    }
                    $company_user = $this->getCompanyUser($company_num, $department_list);
                    if(!$company_user){
                        $this->setError('暂无用户信息');
                        return false;
                    }
                    $list = $this->checkNum($num, $company_user, $is_cooperation);
                } else {//如果公司选择的不是全部
                    $department_id = UserDepartment::find()
                            ->select(['id'])
                            ->where(['name' => $department_name])
                            ->andWhere(['company' =>$company_categroy_id])
                            ->andWhere(['is_show' => 1])
                            ->asArray()
                            ->all();
                    if(!$department_id){
                        $this->setError('暂无部门信息');
                        return false;
                    }
                    for($i = 0; $i < count($department_id); $i++){
                        $department_list[$i] = $department_id[$i]['id'];
                    }
                    $company_user = $this->getCompanyUser([$company_categroy_id], $department_list);
                    if(!$company_user){
                        $this->setError('暂无用户信息');
                        return false;
                    }
                    $list = $this->checkNum($num, $company_user, $is_cooperation);
                }
                
            }
        }
        $result = [
            'day' => $list[1] ? $list[1] : 0,
            'week' => $list[2] ? $list[2] : 0,
            'month' => $list[3] ? $list[3] : 0,
            'yesterday' => $list[4] ? $list[4] : 0,
            'lastweek' => $list[5] ? $list[5] : 0,
            'lastmonth' => $list[6] ? $list[6] : 0,
            'total' => $list[7] ? $list[7] : 0,
        ];
        return $result;
    }
    /**
     * 获取主公司及子公司集合
     * @param unknown $company_id
     */
    
    private function getCompanyFly($company_id, $fly, $department_name)
    {
        if($fly == 0){//如果是主公司
            $company_list = CompanyCategroy::find()//查询子公司     
                        ->select(['id'])
                        ->where(['fly' => $company_id])
                        ->asArray()
                        ->all();
            if(!$company_list){//如果没有子公司
                $company_department = UserDepartment::find()//直接查询对应部门
                            ->select(['id'])
                            ->where(['company_id' => $company_id])
                            ->andWhere(['name' => $department_name])
                            ->andWhere(['is_show' => 1])
                            ->asArray()
                            ->all();
                if(!$company_department){
                    $this->setError('暂无部门信息');exit;
                    return false;
                }
                for($i = 0; $i < count($company_department); $i++){
                    $company_department_id[$i] = $company_department[$i]['id'];
                }
                $company_user = $this->getCompanyUser($company_id, $company_department_id[$i]);
            }  else {//如果有子公司
                $company_num = count($company_list);
                for($i = 0; $i < $company_num; $i++){
                    $company_array[$i] = $company_list[$i]['id'];
                }
                $company_array[$company_num] = $company_id;
                $company_department = UserDepartment::find()//查询公司及子公司部门
                        ->select(['id'])
                        ->where(['name' => $department_name])
                        ->andWhere(['in', 'company', $company_array])
                        ->andWhere(['is_show' => 1])
                        ->asArray()
                        ->all();
                if(!$company_department){
                    $this->setError('暂无部门信息');
                    return false;
                }
                for($i = 0; $i < count($company_department); $i++){
                    $company_department_id[$i] = $company_department[$i]['id'];
                }
                $company_user = $this->getCompanyUser($company_array, $company_department_id);
            }
            if(!$company_user){
                $this->setError('暂无用户信息');
                return false;
            }
            return $company_user;
        }
    }
    /** 
     * 获取公司人员集合
     * @param unknown $company
     */
    private function getCompanyUser($company, $department_name)
    {
        if(is_string($company)){
            $result = User::find()
                    ->select(['username'])
                    ->where(['company_categroy_id' => $company])
                    ->andWhere(['in', 'department_id', $department_name])
                    ->asArray()
                    ->all();
        } else if(is_array($company)){
            $result = User::find()
                    ->select(['username'])
                    ->where(['in', 'company_categroy_id', $company])
                    ->andWhere(['in', 'department_id', $department_name])
                    ->asArray()
                    ->all();
        }
        if(!$result){
            $this->setError('暂无用户信息');
            return false;
        }
        for($i = 0; $i < count($result); $i++){
            $list[$i] = $result[$i]['username'];
        }
        return json_encode($list);
    }
    /**
	 * 根据num值，调取不同的接口
	 * @param  [type] $num [1 拜访客户 2 注册数量 3 自己注册  4订单数量 5 订单金额 6 订单用户数量 7 买买金订单量 8 买买金订单金额 9 买买金订单用户量  10 预存款订单金额]
	 * @return [type]      [description]
	 */
	private function checkNum($num, $department_user, $is_cooperation)
	{
		$result = [];
		switch ($num) {
			case '1':
				for($i = 1; $i < 8; $i++){
					$result[$i] = $this->visitData($department_user, $i, $is_cooperation);
				}
				break;
			case '2':
				for($i = 1; $i < 8; $i++){
					$result[$i] = $this->register($department_user, $i, $is_cooperation);
				}
				break;
			case '3':
				for($i = 1; $i < 8; $i++){
					$result[$i] = $this->register($department_user, $i, $is_cooperation);
				}
				break;
			case '4':
				for($i = 1; $i < 8; $i++){
					$list[$i] = $this->orderNum($department_user, $i, $is_cooperation);
					$result[$i] = $list[$i]['num'];
				}
				break;
			case '5':
				for($i = 1; $i < 8; $i++){
					$list[$i] = $this->orderNum($department_user, $i, $is_cooperation);
					$result[$i] = $list[$i]['amount'];
				}
				break;
			case '6':
				for($i = 1; $i < 8; $i++){
					$result[$i] = $this->orderUser($department_user, $i, $is_cooperation);
				}
				break;
			case '7':
				for($i = 1; $i < 8; $i++){
					$list[$i] = $this->buyOrderNum($department_user, $i, $is_cooperation);
					$result[$i] = $list[$i]['num'];
				}
				break;
			case '8':
				for($i = 1; $i < 8; $i++){
					$list[$i] = $this->buyOrderNum($department_user, $i, $is_cooperation);
					$result[$i] = $list[$i]['amount'];
				}
				break;
			case '9':
				for($i = 1; $i < 8; $i++){
					$result[$i] = $this->buyOrderUser($department_user, $i, $is_cooperation);
				}
				break;
			case '10':
				for($i = 1; $i < 8; $i++){
					$result[$i] = $this->preOrderNum($department_user, $i, $is_cooperation);
				}
				break;
		}
		return $result;
	}
	/**
	 * 找到部门内所有人员
	 * @param  [type] $department 部门集合
	 * @return [strting]             
	 */
	private function departmentUser($department)
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
				->select(['username'])
				->where(['in', 'department_id', $department])
				->andWhere(['in', 'domain_id', $domain_id])
				->asArray()
				->all();
		if(!$data){
			$this->setError('该部门暂无人员');
			return false;
		}
		for($i = 0; $i < count($data); $i++){
			$list[$i] = $data[$i]['username'];
		}
		return json_encode($list);
	}
	/**
	 * 获取注册用户相关数据接口
	 * @param  [type] $user  [json的用户集合]
	 * @param  [type] $type  [类型 1本日 2 本周 3本月 4昨日 5 上周 6上月 7从09年-今天]
	 * @return [type]        [description]
	 */
	private function register($user, $type, $is_cooperation)
	{
		$time = $this->getTime($type);
    	$data = [
    		'staff_id' => $user,
    		'start' => $time[0],
    		'end' => $time[1],
		];
		if($is_cooperation == 0){
		    $list = Shop::find()
    		    ->select(['COUNT(id) as result'])
    		    ->where(['in', 'user_name', $user]);
		} else {
		    $list = \Yii::$app->api->request($this->api,$data);
		}
		$result = $list['result'];
		return $result;
	}
	/**
     * 业务记录拜访商家查询
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    private function visitData($user, $type, $is_cooperation)
    {
    	$user = json_decode($user);
    	$time = $this->getTime($type);
        
        $domainId = \Yii::$app->user->identity->domainId;
        $rows = (new \yii\db\Query())
        ->select('count(id) as visitNum')
        ->from(ShopNote::tableName())
        ->andWhere(['in', 'user', $user])
        ->andWhere('time >= :start and time < :end', [':start'=>$time[0], ':end'=>$time[1]])
        ->one(\Yii::$app->dbofficial);
        if($is_cooperation == 0){
            $nums = CompanyBusinessNotes::find()
                    ->andWhere(['in', 'staff_num', $user])
                    ->andWhere('time >= :start and time < :end', [':start'=>$time[0], ':end'=>$time[1]])
                    ->count();
        } else {
            $nums = UserBusinessNotes::find()
                    ->andWhere(['in', 'staff_num', $user])
                    ->andWhere('time >= :start and time < :end', [':start'=>$time[0], ':end'=>$time[1]])
                    ->count();
        }
        $row = array();
        $row['visitNum'] = $rows['visitNum']+$nums;
        return $row['visitNum'];
    }
    /**
     * 获取订单数量及订单金额
     * @param  [type] $user [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function orderNum($user,$type, $is_cooperation)
    {
    	$time = $this->getTime($type);
    	$data = [
    		'staff_id' => $user,
    		'start' => $time[0],
    		'end' => $time[1],
    		'status' => "'active','finish'",
    		'payment' => '0',
    		'type' => '0',
    	];
    	if($is_cooperation == 0){
    	    $list = Shop::find()
    	           ->select(['COUNT(id) as result'])
    	           ->where(['in', 'user_name', $user]);
    	} else {
    	    $list = \Yii::$app->api->request($this->order_api,$data);
    	}
    	$result = $list['result'];
    	return $result;

    }
    /**
     * 获取订单用户数
     * @param  [type] $user [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function orderUser($user, $type, $is_cooperation)
    {
    	$time = $this->getTime($type);
    	$data = [
    		'staff_id' => $user,
    		'start' => $time[0],
    		'end' => $time[1],
    		'status' => "'active','finish'",
    		'payment' => '0',
    		'type' => '1',
    	];
    	if($is_cooperation == 0){
    	    $list = Goods::find()
    	           ->select(['COUNT(id) as result'])
    	           ->where(['user_name', $user]);
    	} else {
    	    $list = \Yii::$app->api->request($this->order_api,$data);
    	}
    	
    	$result = count($list['result']);
    	return $result;
    }
    /**
     * 获取买买金订单数量及订单金额
     * @param  [type] $user [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function buyOrderNum($user, $type, $is_cooperation)
    {
    	$time = $this->getTime($type);
    	$data = [
    		'staff_id' => $user,
    		'start' => $time[0],
    		'end' => $time[1],
    		'status' => "'active','finish'",
    		'payment' => '63',
    		'type' => '0',
    	];
    	if($is_cooperation == 0){
    	    $list = Goods::find()
            	    ->select(['COUNT(orders_money) as result'])
            	    ->where(['user_name', $user]);
    	} else {
    	    $list = \Yii::$app->api->request($this->order_api,$data);
    	}
    	
    	$result = $list['result'];
    	return $result;
    }
    /**
     * 获取买买金订单用户量
     * @param  [type] $user [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function buyOrderUser($user, $type, $is_cooperation)
    {
    	$time = $this->getTime($type);
    	$data = [
    		'staff_id' => $user,
    		'start' => $time[0],
    		'end' => $time[1],
    		'status' => "'active','finish'",
    		'payment' => '63',
    		'type' => '1',
    	];
    	if($is_cooperation == 0){
    	    $list = Goods::find()
                    ->select(['id'])
                    ->where(['user_name', $user])
                    ->groupBy('user_name')
                    ->asArray()
                    ->all();
            $result = count($list);
    	} else {
    	    $list = \Yii::$app->api->request($this->order_api,$data);
    	    $result = count($list['result']);
    	}
    	return $result;
    }
    private function preOrderNum($user, $type, $is_cooperation)
    {
    	$time = $this->getTime($type);
    	$data = [
    		'staff_id' => $user,
    		'start' => $time[0],
    		'end' => $time[1],
    		'status' => "'active','finish'",
    		'payment' => '51',
    		'type' => '0',
     	];
    	if($is_cooperation == 0){
    	    $result = 0;
    	} else {
    	    $list = \Yii::$app->api->request($this->order_api,$data);
    	    $result = $list['result']['amount'];
    	}
    	
    	return $result;
    }
    public function getTime($type)
    {
    	if($type == '1'){
			$start = DateHelper::getTodayStartTime();
        	$end = DateHelper::getTodayEndTime();
		}
		if($type == '2'){
			$start = DateHelper::getWeekStartTime(0);
			$end = DateHelper::getThisWeekEndTime(0);
		}
		if($type == '3'){
			$start = DateHelper::getMonthStartTime(0);
			$end = DateHelper::getMonthEndTime(0);
		}
		if($type == '4'){
			$start = DateHelper::getYesterdayStartTime(0);
			$end = DateHelper::getYesterdayEndTime(0);
		}
		if($type == '5'){
			$start = mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
			$end = mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
		}
		if($type == '6'){
			$start = DateHelper::getPreMonthStartTime(0);
			$end = DateHelper::getPreMonthEndTime(0);
		}
		if($type == '7'){
			$start = 1230739200;
			$end = time();
		}
		return array($start, $end);
    }
}