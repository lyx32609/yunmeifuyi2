<?php
namespace app\services;

use app\foundation\Service;
use app\mdoels\Regions;
use app\models\Problem;
use app\models\User;
use app\models\UserDepartment;
use app\models\UserDomain;
use app\models\ProviceCity;
class GetBusinessProblemService extends Service
{
	/**
	 * H5 业务问题统计
	 * @param  [type] $user_id    [用户ID]
	 * @param  [type] $area       [省份名称]
	 * @param  [type] $city       [城市名称]
	 * @param  [type] $department [部门名称]
	 * @param  [type] $startTime  [开始时间]
	 * @param  [type] $endTime    [结束时间]
	 * @return [type]             [description]
	 */
	public function getBusinessProblem($user_id, $area, $city, $department, $startTime = null, $endTime = null)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$department){
			$this->setError('部门不能为空');
			return false;
		}
		if(!$startTime) {
			$startTime = $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		}
		if(!$endTime) {
			$endTime = 	$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		}
		if($area) {
			if($area == '全国') { 
				$have = Problem::find()
						->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
						->where(['between','create_time', $startTime, $endTime])
						->andWhere(['department' => $department])
						->andWhere(['=', 'collaboration_department', 'null'])
						->asArray()
						->all();
				$all = Problem::find()
						->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
						->where(['between','create_time', $startTime, $endTime])
						->andWhere(['department' => $department])
						->andWhere(['!=', 'collaboration_department', 'null'])
						->asArray()
						->all();
				$area_all = ProviceCity::find()
						->select(['province_name'])
						->asArray()
						->groupBy(['province_name'])
						->all();
				
				$result = [
					'num' => count($have),
					'collaboration' => count($all),
					'province' => $area_all,
				];
			}
			if($area != '全国'){
				if($city == '全部'){
					$have = Problem::find()
							->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
							->where(['between','create_time', $startTime, $endTime])
							->andWhere(['area' => $area])
							->andWhere(['department' => $department])
							->andWhere(['=', 'collaboration_department', 'null'])
							->asArray()
							->all();
					$all = Problem::find()
							->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
							->where(['between','create_time', $startTime, $endTime])
							->andWhere(['area' => $area])
							->andWhere(['department' => $department])
							->andWhere(['!=', 'collaboration_department', 'null'])
							->asArray()
							->all();
					$area_all = ProviceCity::find()
							->select(['city_name'])
							->where(['province_name' => $area])
							->asArray()
							->all();
					
					$result = [
						'num' => count($have),
						'collaboration' => count($all),
						'city' => $area_all,
					];
				}
				if($city != '全部'){
					$have = Problem::find()
							->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
							->where(['between','create_time', $startTime, $endTime])
							->andWhere(['area' => $area])
							->andWhere(['city' => $city])
							->andWhere(['department' => $department])
							->andWhere(['=', 'collaboration_department', 'null'])
							->asArray()
							->all();
					$all = Problem::find()
							->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
							->where(['between','create_time', $startTime, $endTime])
							->andWhere(['area' => $area])
							->andWhere(['city' => $city])
							->andWhere(['department' => $department])
							->andWhere(['!=', 'collaboration_department', 'null'])
							->asArray()
							->all();
					$result = [
						'num' => count($have),
						'collaboration' => count($all),
					];
				}
			}
		}	
		return $result;
	}
}