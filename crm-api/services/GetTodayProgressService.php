<?php
namespace app\services;

use app\foundation\Service;
use app\models\TomorrowPlan;
use app\benben\DateHelper;
use app\services\UserRecordService;
use app\models\ShopNote;
use app\models\UserBusinessNotes;
use app\services\UserRecordNewService;
use app\models\Shop;
use app\models\Goods;
use app\models\CompanyGoods;
use app\models\CompanyBusinessNotes;
class GetTodayProgressService extends Service
{
	private $api = 'statistics/statisticsDataTimer'; //调取定时任务接口
	private $pay_api = 'statistics/advance'; //调取预存款接口
	/**
	 * 获取今日进展
	 * @param  [type] $user_id       [用户ID]
	 * @param  [type] $specification [1日报，2周报，3月报,默认为1]
	 * @return [type]                [description]
	 */
	public function getTodayProgress($user_id, $specification, $is_cooperation)
	{
		if(!$user_id) {
			$this->setError('用户不能为空');
			return false;
		}
		if(!$specification) {
			$specification = 1;
		}
		if($is_cooperation == '0'){
		    if($specification == 1){
		        $startTime = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
		        $endTime = mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
		    } else if ($specification == 2){
		        $startTime = mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
		        $endTime = mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
		    }else if ($specification == 3){
		        $startTime = mktime(0, 0 , 0,date("m")-1,1,date("Y"));
		        $endTime =  mktime(23,59,59,date("m") ,0,date("Y"));
		    }
		    $plan = $this->getProgress($user_id, $startTime, $endTime, $specification);
		    $visit = $this->visitDataNew($user_id, $startTime, $endTime, $is_cooperation);
		    $register = $this->register($user_id, $startTime, $endTime);
		    $goods = $this->getGoods($user_id, $startTime, $endTime);
		    $visit_clent = [
		        'plan' => $plan['visit_clent'] ? $plan['visit_clent'] : 0,
		        'actual' => $visit ? $visit : 0,
		        'increase' => $visit - $plan['visit_clent'] ? $visit - $plan['visit_clent'] : 0,
		        'proportion' => $this->sprintf($visit, $plan['visit_clent']) ,
		    ];
		    $register_num = [
		        'plan' => $plan['register_num'] ? $plan['register_num'] : 0,
		        'actual' => $register ? $register : 0,
		        'increase' => $register - $plan['register_num'] ? $register - $plan['register_num'] : 0,
		        'proportion' => $this->sprintf($register , $plan['register_num']) ,
		    ];
		    $register_self = [
		        'plan' => $plan['register_self'] ? $plan['register_self'] : 0,
		        'actual' => '',
		        'increase' => '',
		        'proportion' => 0 . '%',
		    ];
		    $register_spread = [
		        'plan' => $plan['register_spread'] ? $plan['register_spread'] : 0,
		        'actual' => '',
		        'increase' => '',
		        'proportion' => 0 . '%',
		    ];
		    $orders_num = [
		        'plan' => $plan['orders_num'] ? $plan['orders_num'] : 0,
		        'actual' => $goods['num'] ? $goods['num'] : 0,
		        'increase' => $goods['num'] - $plan['orders_num'] ? $goods['num'] - $plan['orders_num'] : 0,
		        'proportion' => $this->sprintf($goods['num'] , $plan['orders_num']),
		    ];
		    $orders_money = [
		        'plan' => $plan['orders_money'] ? $plan['orders_money'] : 0,
		        'actual' => $goods['money'] ? $goods['money'] : 0,
		        'increase' => $goods['money'] - $plan['orders_num'] ? $goods['num'] - $plan['orders_num'] : 0,
		        'proportion' => $this->sprintf($goods['money'] , $plan['orders_num']),
		    ];
		    $pre_deposit = [
		        'plan' => $plan['pre_deposit'] ? $plan['pre_deposit'] : 0,
		        'actual' => '',
		        'increase' => '',
		        'proportion' => 0 . '%',
		    ];
		    $pre_money = [
		        'plan' => $plan['pre_money'] ? $plan['pre_money'] : 0,
		        'actual' => '',
		        'increase' => '',
		        'proportion' => 0 . '%',
		    ];
		    return $result = [
		        'visit_clent' => $visit_clent,
		        'register_num' => $register_num,
		        'register_self' => $register_self,
		        'register_spread' => $register_spread,
		        'orders_num' => $orders_num,
		        'orders_money' => $orders_money,
		        // 'pre_deposit' => $pre_deposit,
		        // 'pre_money' => $pre_money,
		    ];
		    
		} else {
		    $arr = [1,2,3,4,5,6,7,8];
		    $data = [];
            for($i = 1; $i < count($arr) + 1; $i++){
		 		$data[$i] = $this->record($user_id, 0, $i);
		 	}
		}
		
		if($specification == 1) {
			$startTime = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
			$endTime = mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
			$plan = $this->getProgress($user_id, $startTime, $endTime, $specification);
		 	$visit_clent = [
				'plan' => $plan['visit_clent'] ? $plan['visit_clent'] : 0,
				'actual' => $data[1]['VisitUser']['day'] ? $data[1]['VisitUser']['day'] :0,
				'increase' => $data[1]['VisitUser']['day'] - $plan['visit_clent'],
				'proportion' => $this->sprintf($data[1]['VisitUser']['day'] , $plan['visit_clent']),
			];
			$register_num = [
				'plan' => $plan['register_num'] ? $plan['register_num'] : 0,
				'actual' => $data[2]['registerNum']['day'] ? $data[2]['registerNum']['day'] : 0,
				'increase' => $data[2]['registerNum']['day'] - $plan['register_num'],
				'proportion' => $this->sprintf($data[2]['registerNum']['day'] , $plan['register_num']),
			];
			$register_self = [
				'plan' => $plan['register_self'] ? $plan['register_self'] : 0,
				'actual' => $data[3]['registerNum']['day'] ? $data[3]['registerNum']['day'] : 0,
				'increase' => $data[3]['registerNum']['day'] - $plan['register_self'],
				'proportion' => $this->sprintf($data[3]['registerNum']['day'] , $plan['register_self']),
			];
			$register_spread = [
				'plan' => $plan['register_spread'] ? $plan['register_spread'] : 0,
				'actual' => 0,
				'increase' => 0 - $plan['register_spread'],
				'proportion' => $this->sprintf(0 , $plan['register_spread']),
			];
			$orders_num = [
				'plan' => $plan['orders_num'] ? $plan['orders_num'] : 0,
				'actual' => $data[4]['orderNum']['day'] ? $data[4]['orderNum']['day'] : 0,
				'increase' => $data[4]['orderNum']['day'] - $plan['orders_num'],
				'proportion' => $this->sprintf($data[4]['orderNum']['day'] , $plan['orders_num']),
			];
			$orders_money = [
				'plan' => $plan['orders_money'] ? $plan['orders_money'] : 0,
				'actual' => $data[5]['orderPrice']['day'] ? $data[5]['orderPrice']['day'] : 0,
				'increase' => $data[5]['orderPrice']['day'] - $plan['orders_money'],
				'proportion' => $this->sprintf($data[5]['orderPrice']['day'] , $plan['orders_money']),
			];
			$pre_deposit = [
				'plan' => $plan['pre_deposit'] ? $plan['pre_deposit'] : 0,
				'actual' => $data[7]['advanceCount']['day'] ? $data[7]['advanceCount']['day'] : 0,
				'increase' => $data[7]['advanceCount']['day'] - $plan['pre_deposit'],
				'proportion' => $this->sprintf($data[7]['advanceCount']['day'] , $plan['pre_deposit']),
			];
			$pre_money = [
				'plan' => $plan['pre_money'] ? $plan['pre_money'] : 0,
				'actual' => $data[8]['orderAdvance']['day'] ? $data[8]['orderAdvance']['day'] : 0,
				'increase' => $data[8]['orderAdvance']['day'] - $plan['pre_money'],
				'proportion' => $this->sprintf($data[8]['orderAdvance']['day'] , $plan['pre_money']),
			    
			];
		} 

		if($specification == 2) {
			$startTime = mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
			$endTime = mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
			$plan = $this->getProgress($user_id, $startTime, $endTime, $specification);
		 	$visit_clent = [
				'plan' => $plan['visit_clent'] ? $plan['visit_clent'] : 0,
				'actual' => $data[1]['VisitUser']['week'] ? $data[1]['VisitUser']['week'] : 0,
				'increase' => $data[1]['VisitUser']['week'] - $plan['visit_clent'],
				'proportion' => $this->sprintf($data[1]['VisitUser']['week'] , $plan['visit_clent']),
		 	    
		 	];
			$register_num = [
				'plan' => $plan['register_num'] ? $plan['register_num'] : 0,
				'actual' => $data[2]['registerNum']['week'] ? $data[2]['registerNum']['week'] : 0,
				'increase' => $data[2]['registerNum']['week'] - $plan['register_num'],
				'proportion' => $this->sprintf($data[2]['registerNum']['week'] , $plan['register_num']),
			];
			$register_self = [
				'plan' => $plan['register_self'] ? $plan['register_self'] : 0,
				'actual' => $data[3]['registerNum']['week'] ? $data[3]['registerNum']['week'] : 0,
				'increase' => $data[3]['registerNum']['week'] - $plan['register_self'],
				'proportion' => $this->sprintf($data[3]['registerNum']['week'] , $plan['register_self']),
			];
			$register_spread = [
				'plan' => $plan['register_spread'] ? $plan['register_spread'] : 0,
				'actual' => 0,
				'increase' => 0 - $plan['register_spread'],
				'proportion' => $this->sprintf(0 , $plan['register_spread']),
			];
			$orders_num = [
				'plan' => $plan['orders_num'] ? $plan['orders_num'] : 0,
				'actual' => $data[4]['orderNum']['week'] ? $data[4]['orderNum']['week'] : 0,
				'increase' => $data[4]['orderNum']['week'] - $plan['orders_num'],
				'proportion' => $this->sprintf($data[4]['orderNum']['week'] , $plan['orders_num']),
			];
			$orders_money = [
				'plan' => $plan['orders_money'] ? $plan['orders_money'] : 0,
				'actual' => $data[5]['orderPrice']['week'] ? $data[5]['orderPrice']['week'] : 0,
				'increase' => $data[5]['orderPrice']['week'] - $plan['orders_money'],
				'proportion' => $this->sprintf($data[5]['orderPrice']['week'] , $plan['orders_money']),
			];
			$pre_deposit = [
				'plan' => $plan['pre_deposit'] ? $plan['pre_deposit'] : 0,
				'actual' => $data[7]['advanceCount']['week'] ? $data[7]['advanceCount']['week'] : 0,
				'increase' => $data[7]['advanceCount']['week'] - $plan['pre_deposit'],
				'proportion' => $this->sprintf($data[7]['advanceCount']['week'] , $plan['pre_deposit']),
			];
			$pre_money = [
				'plan' => $plan['pre_money'] ? $plan['pre_money'] : 0,
				'actual' => $data[8]['orderAdvance']['week'] ? $data[8]['orderAdvance']['week'] : 0,
				'increase' => $data[8]['orderAdvance']['week'] - $plan['pre_money'],
				'proportion' => $this->sprintf($data[8]['orderAdvance']['week'] , $plan['pre_money']),
			];
		} 
		if($specification == 3) {
			$startTime = mktime(0, 0 , 0,date("m")-1,1,date("Y"));
			$endTime =  mktime(23,59,59,date("m") ,0,date("Y"));
			$plan = $this->getProgress($user_id, $startTime, $endTime, $specification);
		 	$visit_clent = [
				'plan' => $plan['visit_clent'] ? $plan['visit_clent'] : 0,
				'actual' => $data[1]['VisitUser']['month'] ? $data[1]['VisitUser']['month'] : 0,
				'increase' => $data[1]['VisitUser']['month'] - $plan['visit_clent'],
				'proportion' => $this->sprintf($data[1]['VisitUser']['month'] , $plan['visit_clent']),
			];
			$register_num = [
				'plan' => $plan['register_num'] ? $plan['register_num'] : 0,
				'actual' => $data[2]['registerNum']['month'] ? $data[2]['registerNum']['month'] : 0,
				'increase' => $data[2]['registerNum']['month'] - $plan['register_num'],
				'proportion' => $this->sprintf($data[2]['registerNum']['month']  ,$plan['register_num']),
			];
			$register_self = [
				'plan' => $plan['register_self'] ? $plan['register_self'] : 0,
				'actual' => $data[3]['registerNum']['month'] ? $data[3]['registerNum']['month'] : 0,
				'increase' => $data[3]['registerNum']['month'] - $plan['register_self'],
				'proportion' => $this->sprintf($data[3]['registerNum']['month'] , $plan['register_self']),
			];
			$register_spread = [
				'plan' => $plan['register_spread'] ? $plan['register_spread'] : 0,
				'actual' => 0,
				'increase' => 0 - $plan['register_spread'],
				'proportion' => $this->sprintf(0 , $plan['register_spread']),
			];
			$orders_num = [
				'plan' => $plan['orders_num'] ? $plan['orders_num'] : 0,
				'actual' => $data[4]['orderNum']['month'] ? $data[4]['orderNum']['month'] : 0,
				'increase' => $data[4]['orderNum']['month'] - $plan['orders_num'],
				'proportion' => $this->sprintf($data[4]['orderNum']['month'] , $plan['orders_num']),
			];
			$orders_money = [
				'plan' => $plan['orders_money'] ? $plan['orders_money'] : 0,
				'actual' => $data[5]['orderPrice']['month'] ? $data[5]['orderPrice']['month'] : 0,
				'increase' => $data[5]['orderPrice']['month'] - $plan['orders_money'],
				'proportion' => $this->sprintf($data[5]['orderPrice']['month'] , $plan['orders_money']),
			];
			$pre_deposit = [
				'plan' => $plan['pre_deposit'] ? $plan['pre_deposit'] : 0,
				'actual' => $data[7]['advanceCount']['month'] ? $data[7]['advanceCount']['month'] : 0,
				'increase' => $data[7]['advanceCount']['month'] - $plan['pre_deposit'],
				'proportion' => $this->sprintf($data[7]['advanceCount']['month'] , $plan['pre_deposit']),
			];
			$pre_money = [
				'plan' => $plan['pre_money'] ? $plan['pre_money'] : 0,
				'actual' => $data[8]['orderAdvance']['month'] ? $data[8]['orderAdvance']['month'] : 0,
				'increase' => $data[8]['orderAdvance']['month'] - $plan['pre_money'],
				'proportion' => $this->sprintf($data[8]['orderAdvance']['month'] , $plan['pre_money']),
			];
		}
		return $result = [
			'visit_clent' => $visit_clent,
			'register_num' => $register_num,
			'register_self' => $register_self,
			'register_spread' => $register_spread,
			'orders_num' => $orders_num,
			'orders_money' => $orders_money,
			// 'pre_deposit' => $pre_deposit,
			// 'pre_money' => $pre_money,
		];
	}
	public function getUserProgress($user_id, $type)
	{
		if(!$user_id){
			$this->setError('用户不能为空');
			return false;
		}
		if(!$type){
			$this->setError('日期不能为空');
			return false;
		}
		if($type == '1'){
			$start = DateHelper::getTodayStartTime(0);
			$end = DateHelper::getTodayEndTime(0);
		}
		if($type == '2'){
			$start = DateHelper::getThisWeekStartTime(0);
			$end = DateHelper::getThisWeekEndTime(0);
		}
		if($type == '3'){
			$start = DateHelper::getMonthStartTime(0);
			$end = DateHelper::getMonthEndTime(0);
		}
		$result = $this->getProgress($user_id, $start, $end, $type);
		if(!$result){
			$this->setError('暂无计划数据');
			return false;
		}
		if(!$result['remarks']){
		    $result['remarks'] = '';
		}
		return $result;	
	}
	//获取预存款
	private function payment($staff_id, $start, $end)
	{
		$data = [
			'staff_id' => json_encode($staff_id),
			'start' => $start,
			'end' => $end,
		];
		$list = \Yii::$app->api->request($this->pay_api,$data);
		return $list;
	}
	//查找明日计划
	private function getProgress($user_id, $startTime, $endTime, $specification) {
		$plan = TomorrowPlan::find()
					->select(['visit_clent', 'register_num', 'register_self', 'register_spread', 'orders_num', 'orders_money', 'pre_deposit', 'pre_money', 'remarks'])
					->where(['user_id' => $user_id])
					->andWhere(['between', 'create_time', $startTime, $endTime])
					->andWhere(['specification' => $specification])
					->asArray()
					->one();
		return $plan;
	}
	//调取接口
	private function userRecord($user_id,$specification, $payment) {
		$data = [
				'type' => 10,
				'type_id' => $user_id,
				'payment' => $payment,
				'period' => $specification,
				'order_status' => 1,
			];
		$user_record = \Yii::$app->api->request($this->api,$data);
		if(!$user_record) {
			$this->setError($user_record['msg']);
			return false;
		}
		return $user_record;
	}
	/**
	 * 调取员工业务记录接口
	 * @param  [type] $user 查询人
	 * @param  [type] $type 查询类型：查询本人 1.查询本组 2.查询部门
	 * @return [type]  
	 */
	public function record($user, $type, $num){
		$user_record = new UserRecordNewService;
		$list = $user_record->getUserRecord($user, $type, $num);
		return $list;
	}
	/**
     * 业务记录拜访商家查询（暂时弃用）
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array 
     */
	private  function visitData($user,$start,$end)
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
    /**
     * 业务记录拜访商家查询
     * @param int $user 查询人
     * @param string $start: 开始时间  $end: 结束时间
     * @return array
     */
    private  function visitDataNew($user, $start, $end, $is_cooperation)
    {
        
        $domainId = \Yii::$app->user->identity->domainId;
        $rows = (new \yii\db\Query())
        ->select('count(id) as visitNum')
        ->from(ShopNote::tableName())
        ->andWhere(['user' => $user])
        ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
        ->one(\Yii::$app->dbofficial);
        if($is_cooperation == 0){
            $nums = CompanyBusinessNotes::find()
                    ->andWhere(['staff_num' => $user])
                    ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
                    ->count();
        } else {
            $nums = UserBusinessNotes::find()
                    ->andWhere(['staff_num' => $user])
                    ->andWhere('time >= :start and time < :end', [':start'=>$start, ':end'=>$end])
                    ->count();
        }
        $row = array();
        $row['visitNum'] = $rows['visitNum']+$nums;
        return $row['visitNum'];
    }
    /**
     * 获取注册数量
     * @param  $user        用户
     * @param  $start       开始时间
     * @param  $end         结束时间
     */
    private function register($user, $start, $end)
    {
        $result = Shop::find()      
                ->select(['id'])
                ->where(['user_id' => $user])
                ->andWhere(['between', 'createtime', $start, $end])
                ->asArray()
                ->all();
        if($result){
            return count($result);
        }
        return 0;
    }
    private function getGoods($user, $start, $end)
    {
        $result = Goods::find()
                ->select(['id', 'orders_money'])
                ->where(['user_id' => $user])
                ->andWhere(['between', 'createtime', $start, $end])
                ->asArray()
                ->all();
        if($result){
            for($i = 0; $i < count($result); $i++){
                $money['money'] += $result[$i]['goods_money']; 
            }
            return [
                'money' => $money['money'],
                'num' => count($result),
            ];
        }
        return 0;
    }
    /**
     * 取小数点后2位并四舍五入
     * @param unknown $start
     * @param unknown $end
     */
    public function sprintf($start, $end)
    {
        if(($start &&  $end == '0') ||  ($start &&  !$end)){ //实际有数据但计划无数据
            return 100 . '%';
        } else if(($start == '0' && $end) || (!$start && $end)){// 实际无数据但计划有数据
            return 0 . '%';
        } else if(!$start && !$end) {
            return 0 . '%';
        }
        return (sprintf("%.2f", $start / $end) * 100) . '%';
    }
}

