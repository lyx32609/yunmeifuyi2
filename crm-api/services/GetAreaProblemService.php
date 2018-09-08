<?php
namespace app\services;

use app\foundation\Service;
use app\mdoels\Regions;
use app\models\Problem;
use app\models\User;
use app\models\UserDepartment;
use app\models\UserDomain;
use app\models\ProviceCity;
class GetAreaProblemService extends Service
{
	/**
	 * 获取省市业务问题统计
	 * @param  [type] $user_id    [用户ID]
	 * @param  [type] $area       [省份名称]
	 * @param  [type] $city       [城市名称]
	 * @param  [type] $department [部门名称]
	 * @param  [type] $startTime  [开始时间]
	 * @param  [type] $endTime    [结束时间]
	 * @return [type]             [description]
	 */
	public function getAreaProblem($user_id, $area, $city, $department, $startTime = null, $endTime = null)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$startTime) {
			$startTime = mktime(0,0,0,date('m'),date('d'),date('Y'));
		}
		if(!$endTime) {
			$endTime = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		}
		if($area) {
			$have = Problem::find()
					->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['!=', 'collaboration_department', 'null'])
					->andWhere(['area' => $area])
					->asArray()
					->all();
			$all = Problem::find()
					->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['=', 'collaboration_department', 'null'])
					->andWhere(['area' => $area])
					->asArray()
					->all();
			$result = [
				'num' => count($have),
				'collaboration' => count($all),
				'list' => $have,
			];
		} else if($city) {
			$have = Problem::find()
					->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['!=', 'collaboration_department', 'null'])
					->andWhere(['area' => $area])
					->andWhere(['city' => $city])
					->asArray()
					->all();
			$all = Problem::find()
					->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['=', 'collaboration_department', 'null'])
					->andWhere(['area' => $area])
					->andWhere(['city' => $city])
					->asArray()
					->all();
			$result = [
					'num' => count($have),
					'collaboration' => count($all),
					'list' => $have,
				];
		} else if($department) {
			$have = Problem::find()
					->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['!=', 'collaboration_department', 'null'])
					->andWhere(['area' => $area])
					->andWhere(['city' => $city])
					->andWhere(['department' => $department])
					->asArray()
					->all();
			$all = Problem::find()
					->select(['problem_id', 'problem_title', 'priority', 'collaboration_department', 'create_time'])
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['=', 'collaboration_department', 'null'])
					->andWhere(['area' => $area])
					->andWhere(['city' => $city])
					->andWhere(['department' => $department])
					->asArray()
					->all();
			$result = [
					'num' => count($have),
					'collaboration' => count($all),
					'list' => $have,
				];
		}
		return $result;
	}
}