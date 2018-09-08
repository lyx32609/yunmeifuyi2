<?php
namespace app\services;

use app\foundation\Service;
use app\benben\DateHelper;
use app\models\UserDomain;
use app\models\UserDepartment;

class GetRankListService extends Service
{
	private $api = 'statistics/statisticsDataTimer';
	/**
	 * 获取排名指标
	 * @param  [type] $area          [省份]
	 * @param  [type] $city          [城市]
	 * @param  [type] $department    [部门]
	 * @param  [type] $rank          [指标]
	 * @param  [type] $specification [日期，1:本日 2:本周 3:本月]
	 * @return [type]                [description]
	 */
	public function getRankList($area, $city, $department, $rank, $specification)
	{
		$domain_id = UserDomain::find()
				->select(['domain_id'])
				->where(['region' => $city])
				->asArray()
				->one();
		if(!$domain_id) { 
			$this->setError('该城市暂无相应部门');
			return false;
		}
		$department_id = UserDepartment::find()
				->select(['department_id'])
				->where(['domain_id' => $domain_id['domain_id']])
				->andWhere(['desc' => $department]) 
				->andWhere(['is_show' => 1])
				->asArray()
				->one();
		

	}
	/**
	 * 调取计划任务数据接口
	 */
	public function userRecord($type, $type_id, $pament, $period)
	{
		$data = [
				'type' => $type,
				'type_id' => $type,
				'payment' => $payment,
				'period' => $period,
				'order_status' => 1,
		];
		$list = \Yii::$app->api->request($this->api,$data);
		if(!$list) {
			$this->setError($list['msg']);
			return false;
		}
		return $list;
	}
}