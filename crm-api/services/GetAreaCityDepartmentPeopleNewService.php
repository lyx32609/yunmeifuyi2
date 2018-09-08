<?php
namespace app\services;

use app\foundation\Service;
use app\models\Regions;
use app\models\UserDomain;
use app\models\UserDepartment;
use app\models\User;
use app\models\UserGroup;
use app\services\GetDepartmentStaffService;
class GetAreaCityDepartmentPeopleNewService extends Service
{
	/**
	 * 根据城市名称返回对应部门
	 * @param  [type] $city       [城市名称]
	 * @param  [type] $company_category_id       [当前登录人所属公司id]
	 */
	public function getAreaCityDepartmentPeople($city,$company_category_id)
	{
		if(!$city || !$company_category_id){
			$this->setError('参数不能为空');
			return false;
		}
		
		$regions = Regions::find()
				->select(['region_id'])
				->where(['like', 'local_name', $city])
				->asArray()
				->one();
		if(!$regions){
			$this->setError('该城市暂未开通');
			return false;
		}
		$result = UserDepartment::find()
				->select(['id', 'name'])
				->where(['domain_id' => $regions['region_id']])
				->andWhere(['company'=>$company_category_id])
				->andWhere(['is_show' => 1])
				->asArray()
				->all();
		if(!$result){
			$this->setError('该城市暂无部门');
			return false;
		}
		for($i = 0; $i < count($result); $i++){
			$result[$i]['user_num'] = $this->getDepartmentUser($result[$i]['id']);
			$result[$i]['group'] = $this->getDepartmentStaff($result[$i]['id']);
		}
		return $result;
	}
	/**
	 * 获取组或部门人员
	 * @param  [type] $department [description]
	 * @return [type]             [description]
	 */
	public function getDepartmentStaff($department)
	{
		$group_department = UserGroup::find()
				->select(['id', 'name'])
				->where(['department_id' => $department])
				->asArray()
				->all();
		if($group_department) {
			$result = UserGroup::find()
				->select(UserGroup::tableName().'.id,name')
				->with(['users' => function (\yii\db\ActiveQuery $query) {
              			$query->select('group_id, id,name');
          			}])
				->where(UserGroup::tableName() .'.department_id = :department',[':department' => $department])
				->asArray()
				->all();
		} else { 
			$data = User::find()
					->select(['id', 'name'])
					->where(['department_id' => $department])
					->orderBy('is_staff desc')
					->asArray()
					->all();
			$result = [[
				'id' => 0,
				'name' => '部门人员',
				'users' => $data,
			]];
		}
		return $result;
	}
	/**
	 * 获取部门人员
	 * @param  [type] $department [description]
	 * @return [type]             [description]
	 */
	public function getDepartmentUser($department)
	{
		$group_department = UserGroup::find()
				->select(['id', 'name'])
				->where(['department_id' => $department])
				->asArray()
				->all();
		if(!$group_department){
			$data = User::find()
				->select(['id', 'name'])
				->where(['department_id' => $department])
				->orderBy('is_staff desc')
				->asArray()
				->all(); 
		} else {
			for($i = 0; $i < count($group_department); $i++){
				$list[$i] = $group_department[$i]['id'];
			}
			$data = User::find()
					->select(['id', 'name'])
					->where(['in', 'group_id', $list])
					->orderBy('is_staff desc')
					->asArray()
					->all(); 
		}
		return count($data) ? count($data) : 0;
	}
}