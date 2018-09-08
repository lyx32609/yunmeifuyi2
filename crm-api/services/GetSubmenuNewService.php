<?php
namespace app\services;

use app\foundation\Service;
//use app\mdoels\Regions;
use app\models\Problem;
use app\models\UserDepartment;
use app\models\ProviceCity;
use app\models\User;
use app\models\Regions;
class GetSubmenuNewService extends Service
{
	/**
	 * 根据id,查询管理员账号显示问题列表
	 * @param  [type] $user_id    [用户ID]
	 * @param  [type] $area       [省份名称]
	 * @param  [type] $city       [城市名称]
	 * @param  [type] $department [部门名称]
	 * @param  [type] $specification [规格： 1本日  2本周 3 本月]
	 * @return [type]             [description]
	 */
	public function getSubmenu($user_id, $area, $city, $department, $specification)
	{
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$specification){
			$specification = 1;
		}
		if($specification == 1) {
			//本日
			$startTime = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$endTime = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		} else if($specification == 2) {
			//本周
			$startTime = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
			$endTime = time();
		} else if($specification == 3){
			//本月
			$startTime =mktime(0, 0 , 0,date("m"),1,date("Y"));
			$endTime = time();
		}

		$select = ['problem_id', 'problem_title', 'problem_content', 'user_id', 'priority', 'collaboration_department', 'create_time'];
		if($area == '全国') {
			$have = Problem::find()
					->select($select)
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['department' => $department])
					->andWhere(['!=', 'collaboration_department', 'null'])
					->orderBy('priority desc')
					->asArray()
					->all();
			$all = Problem::find()
					->select($select)
					->where(['between','create_time', $startTime, $endTime])
					->andWhere(['department' => $department])
					->orderBy('priority desc')
					->asArray()
					->all();
		}
		if($area != '全国'){
			if($city == '全部'){
				$have = Problem::find()
						->select($select)
						->where(['between','create_time', $startTime, $endTime])
						->andWhere(['department' => $department])
						->andWhere(['area' => $area])
						->andWhere(['!=', 'collaboration_department', 'null'])
						->orderBy('priority desc')
						->asArray()
						->all();
				$all = Problem::find()
						->select($select)
						->where(['between','create_time', $startTime, $endTime])
						->andWhere(['area' => $area])
						->andWhere(['department' => $department])
						->orderBy('priority desc')
						->asArray()
						->all();
				}
			if($city != '全部'){
				$have = Problem::find()
						->select($select)
						->where(['between','create_time', $startTime, $endTime])
						->andWhere(['department' => $department])
						->andWhere(['area' => $area])
						->andWhere(['city' => $city])
						->andWhere(['!=', 'collaboration_department', 'null'])
						->orderBy('priority desc')
						->asArray()
						->all();
				$all = Problem::find()
						->select($select)
						->where(['between','create_time', $startTime, $endTime])
						->andWhere(['area' => $area])
						->andWhere(['city' => $city])
						->andWhere(['department' => $department])
						->orderBy('priority desc')
						->asArray()
						->all();
			}
		}
		for($i = 0; $i < count($all); $i++) {
			$all[$i]['user'] = User::find()
					->select(['id', 'name', 'department_id', 'domain_id'])
					->where(['id' => $all[$i]['user_id']])
					->asArray()
					->one();
			$all[$i]['department'] = UserDepartment::find()
					->select(['id', 'name'])
					->where(['id' => $all[$i]['user']['department_id']])
					->andWhere(['is_show' => 1])
					->asArray()
					->one();
			// $all[$i]['city'] = ProviceCity::find()
			// 		->select(['province_name', 'city_name'])
			// 		->where(['city_id' => $all[$i]['user']['domain_id']])
			// 		->asArray()
			// 		->one();
			
			$city_name = Regions::find()
					->select(['p_region_id',"local_name"])
					->where(['region_id' => $all[$i]['user']['domain_id']])
					->asArray()
					->one();
			$all[$i]['city']['city_name'] = $city_name['local_name'];
			$province_name = Regions::find()
					->select(['region_id', 'p_region_id',"local_name"])
					->where(['region_id' => $city_name['p_region_id']])
					->asArray()
					->one();
			$all[$i]['city']['province_name'] = $province_name['local_name'];

		}
		for($i = 0; $i < count($all); $i++){
			if($all[$i]['collaboration_department'] == 'null'){
				$all[$i]['collaboration_department'] = '';
			}
		}
		return $result = [
					'num' => count($all),
					'collaboration' => count($have),
					'list' => $all,
				];
		
	}
}