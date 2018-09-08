<?php
namespace app\services;

use app\foundation\Service;
use app\models\Question;
use app\models\User;
use app\models\UserDepartment;
use app\models\Problem;

class QuestionAddService extends Service
{
	/**
	 * 问题补充
	 * @param  [type] $problem_id       [问题ID]
	 * @param  [type] $question_content [补充问题内容]
	 * @param  [type] $user_id		    [用户ID]
	 * @param  [type] $user_name		[用户名]
	 * @return [type]                   [description]
	 */
	public function questionAdd($problem_id, $user_id, $user_name, $question_content)
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
		if(!$question_content){
			$this->setError('补充内容不能为空');
			return false;
		}
		$user = User::find()
				->select(['name', 'department_id', 'rank', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
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
				->one();
		if(!$group['name']) {
			$group['name'] = '0';
		}
		$result = new Question;
		$result->problem_id = $problem_id;
		$result->question_content = $question_content;
		$result->author_id = $user_id;
		$result->author = $user['name'];
		$result->group = $group['name'];
		$result->group_id = $user['department_id'];
		$result->create_time = time();
		$result->company_id = $user['company_categroy_id'];
		if(!$result->save()) {
			$this->setError('补充问题提交失败');
			return false;
		}
		return $result = '补充问题提交成功';
	}
}