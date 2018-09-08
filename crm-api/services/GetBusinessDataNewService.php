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
class GetBusinessDataNewService extends Service
{
	private $api = 'statistics/statisticsMember'; // 注册用户相关数据
	private $order_api = 'statistics/statisticsOrder'; //订单相关数据

	public function getBusinessData($area, $city, $department)
	{
		if(!$area) {
			$this->setError('省份不能为空');
			return false;
		}
		if(!$department){
			$this->setError('部门不能为空');
			return false;
		}
		if($area == '全国'){
			
			$department_all = UserDepartment::find()
					->select(['id','is_select'])
					->where(['name' => $department])
					->andWhere(['is_show' => 1])
					->asArray()
					->all();
			if(!$department_all){
				$this->setError('全国暂无该部门');
			}
			if(count($department_all) > 1){
				for($i = 0; $i < count($department_all); $i++){
					$list[$i] = $department_all[$i]['id']; 
				}
			} else {
				$list[0] = $department_all[0]['id'];
			}
			$department_user = $this->departmentUser($list);
			if(!$department_user){
				$this->setError('该部门暂无人员');
				return false;
			}
			$register = [];
			$visit = [];
			$order_num = [];
			$order_user = [];
			$buy_order_num = [];
			$buy_order_user = [];
			$pre_order_num = [];
 			for($i = 1; $i < 8; $i++){
				$register[$i] = $this->register($department_user,$i);
				$visit[$i] = $this->visitData($department_user,$i);
				$order_num[$i] = $this->orderNum($department_user, $i);
				$order_user[$i] = $this->orderUser($department_user, $i);
				$buy_order_num[$i] = $this->buyOrderNum($department_user, $i);
				$buy_order_user[$i] = $this->buyOrderUser($department_user, $i);
				$pre_order_num[$i] = $this->preOrderNum($department_user, $i);
			}
		}
		if($area != '全国'){
			if($city == '全部'){
				$domain = ProviceCity::find()
						->select(['city_id'])
						->where(['province_name' => $area])
						->asArray()
						->all();
				if(!$domain){
					$this->setError('该省份暂无任何城市');
				}
				for($i = 0; $i < count($domain); $i++){
					$domain_id[$i] = $domain[$i]['city_id']; 
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
				$register = [];
				$visit = [];
				$order_num = [];
				$order_user = [];
				$buy_order_num = [];
				$buy_order_user = [];
				$pre_order_num = [];
	 			for($i = 1; $i < 8; $i++){
					$register[$i] = $this->register($department_user,$i);
					$visit[$i] = $this->visitData($department_user,$i);
					$order_num[$i] = $this->orderNum($department_user, $i);
					$order_user[$i] = $this->orderUser($department_user, $i);
					$buy_order_num[$i] = $this->buyOrderNum($department_user, $i);
					$buy_order_user[$i] = $this->buyOrderUser($department_user, $i);
					$pre_order_num[$i] = $this->preOrderNum($department_user, $i);
				}
				
			}
			if($city != '全部'){
				$domain_id = UserDomain::find()
						->select(['domain_id'])
						->where(['region' => $city])
						->asArray()
						->one();
				if(!$domain_id){
					$this->setError('该城市暂未开通');
					return false;
				}
				$department_all = UserDepartment::find()
						->select(['id'])
						->where(['domain_id' => $domain_id['domain_id']])
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
				$register = [];
				$visit = [];
				$order_num = [];
				$order_user = [];
				$buy_order_num = [];
				$buy_order_user = [];
				$pre_order_num = [];
	 			for($i = 1; $i < 8; $i++){
					$register[$i] = $this->register($department_user,$i);
					$visit[$i] = $this->visitData($department_user,$i);
					$order_num[$i] = $this->orderNum($department_user, $i);
					$order_user[$i] = $this->orderUser($department_user, $i);
					$buy_order_num[$i] = $this->buyOrderNum($department_user, $i);
					$buy_order_user[$i] = $this->buyOrderUser($department_user, $i);
					$pre_order_num[$i] = $this->preOrderNum($department_user, $i);
				}
			}
		}
		$result['visit'] = [
			'day' => $visit[1],
			'week' => $visit[2],
			'month' => $visit[3],
			'yesterday' => $visit[4],
			'lastweek' => $visit[5],
			'lastmonth' => $visit[6],
			'total' => $visit[7],
		];
		$result['register'] = [
			'day' => $register[1],
			'week' => $register[2],
			'month' => $register[3],
			'yesterday' => $register[4],
			'lastweek' => $register[5],
			'lastmonth' => $register[6],
			'total' => $register[7],
		];
		$result['register_self'] = [
			'day' => $register[1],
			'week' => $register[2],
			'month' => $register[3],
			'yesterday' => $register[4],
			'lastweek' => $register[5],
			'lastmonth' => $register[6],
			'total' => $register[7],
		];
		
		$result['order_num'] = [
			'day' => $order_num[1]['num'],
			'week' => $order_num[2]['num'],
			'month' => $order_num[3]['num'],
			'yesterday' => $order_num[4]['num'],
			'lastweek' => $order_num[5]['num'],
			'lastmonth' => $order_num[6]['num'],
			'total' => $order_num[7]['num'],
		];
		$result['order_money'] = [
			'day' => $order_num[1]['amount'] ? $order_num[1]['amount'] : 0,
			'week' => $order_num[2]['amount'] ? $order_num[2]['amount'] : 0,
			'month' => $order_num[3]['amount'] ? $order_num[3]['amount'] : 0,
			'yesterday' => $order_num[4]['amount'] ? $order_num[4]['amount'] : 0,
			'lastweek' => $order_num[5]['amount'] ? $order_num[5]['amount'] : 0,
			'lastmonth' => $order_num[6]['amount'] ? $order_num[6]['amount'] : 0,
			'total' => $order_num[7]['amount'] ? $order_num[7]['amount'] : 0,
		];
		$result['order_user'] = [
			'day' => $order_user[1],
			'week' => $order_user[2],
			'month' => $order_user[3],
			'yesterday' => $order_user[4],
			'lastweek' => $order_user[5],
			'lastmonth' => $order_user[6],
			'total' => $order_user[7],
		];
		$result['buy_order_num'] = [
			'day' => $buy_order_num[1]['num'],
			'week' => $buy_order_num[2]['num'],
			'month' => $buy_order_num[3]['num'],
			'yesterday' => $buy_order_num[4]['num'],
			'lastweek' => $buy_order_num[5]['num'],
			'lastmonth' => $buy_order_num[6]['num'],
			'total' => $buy_order_num[7]['num'],
		];
		$result['buy_order_money'] = [
			'day' => $buy_order_num[1]['amount'] ? $buy_order_num[1]['amount'] : 0,
			'week' => $buy_order_num[2]['amount'] ? $buy_order_num[2]['amount'] : 0,
			'month' => $buy_order_num[3]['amount'] ? $buy_order_num[3]['amount'] : 0,
			'yesterday' => $buy_order_num[4]['amount'] ? $buy_order_num[4]['amount'] : 0,
			'lastweek' => $buy_order_num[5]['amount'] ? $buy_order_num[5]['amount'] : 0,
			'lastmonth' => $buy_order_num[6]['amount'] ? $buy_order_num[6]['amount'] : 0,
			'total' => $buy_order_num[7]['amount'] ? $buy_order_num[7]['amount'] : 0,
		];
		$result['buy_order_user'] = [
			'day' => $buy_order_user[1],
			'week' => $buy_order_user[2],
			'month' => $buy_order_user[3],
			'yesterday' => $buy_order_user[4],
			'lastweek' => $buy_order_user[5],
			'lastmonth' => $buy_order_user[6],
			'total' => $buy_order_user[7],
		];
		$result['pre_order_num'] = [
			'day' => $pre_order_num[1] ? $pre_order_num[1] : 0,
			'week' =>$pre_order_num[2] ? $pre_order_num[2] : 0,
			'month' => $pre_order_num[3] ? $pre_order_num[3] : 0,
			'yesterday' => $pre_order_num[4] ? $pre_order_num[4] : 0,
			'lastweek' => $pre_order_num[5] ? $pre_order_num[5] : 0,
			'lastmonth' => $pre_order_num[6] ? $pre_order_num[6] : 0,
			'total' => $pre_order_num[7] ? $pre_order_num[7] : 0,
		];
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
		$leave = '1';
		if(in_array($leave, $department)){
			$department[count($department) + 1] = '4';
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
	private function register($user, $type)
	{
		$time = $this->getTime($type);
    	$data = [
    		'staff_id' => $user,
    		'start' => $time[0],
    		'end' => $time[1],
		];
		
		$list = \Yii::$app->api->request($this->api,$data);
		$result = $list['result'];
		return $result;
	}
	/**
     * 业务记录拜访商家查询
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    private function visitData($user, $type)
    {
    	$user = json_decode($user);
    	$time = $this->getTime($type);
        if(is_array($user)){
            $user = implode(',', $user);
        }
       
        $domainId = \Yii::$app->user->identity->domainId;
        $rows = (new \yii\db\Query())
        ->select('count(id) as visitNum')
        ->from(ShopNote::tableName())
        ->andWhere('user in ('.$user.')')
        ->andWhere('time >= :start and time < :end', [':start'=>$time[0], ':end'=>$time[1]])
        ->one(\Yii::$app->dbofficial);
    
        $nums = UserBusinessNotes::find()
        ->andWhere('staff_num in ('.$user.')')
        ->andWhere('time >= :start and time < :end', [':start'=>$time[0], ':end'=>$time[1]])
        ->count();
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
    private function orderNum($user,$type)
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
    	$list = \Yii::$app->api->request($this->order_api,$data);
    	$result = $list['result'];
    	return $result;

    }
    /**
     * 获取订单用户数
     * @param  [type] $user [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function orderUser($user, $type)
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
    	$list = \Yii::$app->api->request($this->order_api,$data);
    	$result = count($list['result']);
    	return $result;
    }
    /**
     * 获取买买金订单数量及订单金额
     * @param  [type] $user [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function buyOrderNum($user, $type)
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
    	$list = \Yii::$app->api->request($this->order_api,$data);
    	$result = $list['result'];
    	return $result;
    }
    /**
     * 获取买买金订单用户量
     * @param  [type] $user [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function buyOrderUser($user, $type)
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
    	$list = \Yii::$app->api->request($this->order_api,$data);
    	$result = count($list['result']);
    	return $result;
    }
    private function preOrderNum($user, $type)
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
    	$list = \Yii::$app->api->request($this->order_api,$data);
    	$result = $list['result']['amount'];
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