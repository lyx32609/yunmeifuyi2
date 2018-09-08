<?php
namespace app\services;

use app\foundation\Service;
use app\models\Instruction;
use app\models\User;
use app\models\UserDepartment;
use app\models\Problem;
use app\services\PushMessageService;
class InstructionAddNewService extends Service
{
	/**
	 * 指令补充
	 * @param  [type] $problem_id       [问题ID]
	 * @param  [type] $instruction_content [指令问题内容]
	 * @param  [type] $user_id		    [用户ID]
	 * @return [type]                   [description]
	 */
	public function instructionAdd($problem_id, $user_id, $user_name, $instruction_content, $collaboration_department, $appkey, $appid, $masterSecret)
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
		if(!$instruction_content){
			$this->setError('指令内容不能为空');
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
		if(!$appkey){
			$this->setError('appkey 不能为空');
			return false;
		}
		if(!$appid){
			$this->setError('appid 不能为空');
			return false;
		}
		if(!$masterSecret){
			$this->setError('masterSecret 不能为空');
			return false;
		}
		$user = User::find()
				->select(['name', 'department_id', 'rank', 'company_categroy_id'])
				->where(['id' => $user_id])
				->asArray()
				->one();
		if(!$user) {
			$this->setError('该用户不存在');
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
		$result = new Instruction;
		$result->problem_id = $problem_id;		
		$result->instruction_content = $instruction_content;
		$result->author_id = $user_id;
		$result->author = $user['name'];
		$result->group = $group['name'];
		$result->group_id = $user['department_id'];
		$result->create_time = time();
		$result->company_id = $user['company_categroy_id'];
		if(!$result->save()) {
			//var_dump($result->getErrors());exit;
			$this->setError('指令补充提交失败');
			return false;
		}
		
		if(!$collaboration_department){ 
			$collaboration_department = 'null';
		}
		$problem = Problem::findOne($problem_id);
		$problem->collaboration_department = $collaboration_department;
		if(!$problem->save()){
			$this->setError('协同部门修改失败');
			return false;
		}
		$problem_user = Problem::find()
					->select(['user_id'])
					->where(['problem_id' => $problem_id])
					->asArray()
					->one();
		$cid = User::find()
						->select(['username','department_id'])
						->where(['id' => $problem_user['user_id']])
						->asArray()
						->one();
		if($user['rank'] == '30'){ //如果指令是总经理发送的
			if($collaboration_department){
				$department_all = explode(',', $collaboration_department); //指令下达部门
				$department_all[count($department_all)] = $cid['department_id']; 
				$department_manager = User::find() //指令下达部门经理及问题提交人
						->select(['username','domain_id'])
						->where(['in', 'department_id', $department_all])
						->andWhere(['rank' => '4'])
						->asArray()
						->all();

				$city_manager = User::find()
						->select(['username'])
						->where(['domain_id' => $department_manager[0]['domain_id']])
						->andWhere(['rank' => '3'])
						->asArray()
						->one();
				for($i = 0; $i < count($department_manager); $i++){
					$list[$i] = $department_manager[$i]['username'];
				}
				if($city_manager){
					$list[count($list)] = $city_manager['username'];
				}
				
				if($list){
					$list[count($list)] = $cid['username'];
				} else {
					$list[0] = $cid['username'];
				} 
				
			} else {
				$list[0] = $cid['username'];
			}
		}
		if($user['rank'] == '3'){//如果指令是城市经理发送的
			if($collaboration_department){
				$department_all = explode(',', $collaboration_department); //指令下达部门
				$department_all[count($department_all)] = $cid['department_id']; 
					$department_manager = User::find() //指令下达部门经理及问题提交人
							->select(['username'])
							->where(['in', 'department_id', $department_all])
							->andWhere(['rank' => '4'])
							->asArray()
							->all();
				for($i = 0; $i < count($department_manager); $i++){
						$list[$i] = $department_manager[$i]['username'];
				}
				
				if($list){
					$list[count($list)] = $cid['username'];
				} else {
					$list[0] = $cid['username'];
				} 
			} else { 
				$list[0] = $cid['username'];
			}
		}
		// if($list){
		// 	$data = $list;
		// 	$push = new PushMessageService; 
		// 	$pushMessage = $push->push($appkey, $appid, $masterSecret, $data, $problem_title, '你有一条新的问题信息');
				
		// }
		return $result = '指令补充提交成功';
	}
}