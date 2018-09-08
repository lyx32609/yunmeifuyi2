<?php
namespace app\services;

use app\foundation\Service;
use app\models\Collaboration;
use app\models\User;
use app\models\UserDepartment;
use app\models\Problem;
class CollaborationAddService extends Service
{
	/**
	 * 协同补充
	 * @param  [type] $problem_id       [问题ID]
	 * @param  [type] $collaboration_content [协同问题内容]
	 * @param  [type] $user_id		    [用户ID]
	 * @return [type]                   [description]
	 */
	public function questionAdd($problem_id, $user_id, $user_name, $collaboration_content)
	{

		if(!$problem_id) {
			$this->setError('问题ID不能为空');
			return false;
		}
		if(!$user_id) {
			$this->setError('用户ID不能为空');
			return false;
		}
		if(!$user_name) {
			$this->setError('用户名不能为空');
			return false;
		}
		if(!$collaboration_content){
			$this->setError('协同内容不能为空');
			return false;
		}
		$user = User::find()
				->select(['name', 'department_id', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		if(!$user){
			$this->setError('用户不存在');
			return false;
		}
		
		$problem_lock = Problem::find()
				->select(['problem_lock'])
				->where(['problem_id' => $problem_id])
				->asArray()
				->one();
		if($problem_lock['problem_lock'] == 1) {
			$this->setError('该问题已解决');
			return false;
		}
	
		$group = UserDepartment::find()
				->select(['name'])
				->where(['id' => $user['department_id']])
				->andWhere(['is_show' => 1])
				->asArray()
				->one();
		if(!$group['name']) {
			$this->setError('部门查询失败');
			return false;
		}
		$result = new Collaboration;
		$result->author = $user['name'];
		$result->author_id = $user_id;
		$result->group = $group['name'];
		$result->group_id = $user['department_id'];
		$result->problem_id = $problem_id;
		$result->collaboration_content = $collaboration_content;
		$result->create_time = time();
		$result->company_id = $user['company_categroy_id'];
		if(!$result->save()) {
			//var_dump($result->getErrors());exit;
			$this->setError('协同问题提交失败');
			return false;
		}
		return $result = '协同问题提交成功';
	}
}