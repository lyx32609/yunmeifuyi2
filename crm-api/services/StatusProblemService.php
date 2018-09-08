<?php
namespace app\services;

use app\foundation\Service;
use app\models\ProblemStatus;
class StatusProblemService extends Service
{
	/**
	 * 记录用户查看问题
	 * @param  [type] $user_id   [用户id]
	 * @param  [type] $status_id [问题id]
	 * @param  [type] $status    [1 问题 2协同 3指令]
	 * @return [type]            [description]
	 */
	public function statusProblem($user_id, $status_id, $status)
	{
		if(!$user_id){
			$this->setError('用户id不能为空');
			return false;
		}
		if(!$status_id){
			$this->setError('类型id不能为空');
			return false;
		}
		if(!$status){
			$this->setError('类型不能为空');
			return false;
		}
		$ret  = ProblemStatus::find()
				->where(['user_id' => $user_id])
				->andWhere(['status_id' => $status_id])
				->andWhere(['status' => $status])
				->asArray()
				->one();
		if($ret){
			$this->setError('该条记录已经存在');
			return false;
		}
		$result = new ProblemStatus;
		$result->user_id = $user_id;
		$result->status_id = $status_id;
		$result->status = $status;
		$result->createtime = time();
		if(!$result->save()){
			$this->setError('记录失败');
			return false;
		}
		return $result = '记录成功';
	}
}