<?php
namespace app\services;

use app\foundation\Service;
use app\models\Question;
use app\models\Instruction;
use app\models\Collaboration;
class GetQusetionDetailsService extends Service
{
	/**
	 * 业务问题详情列表接口
	 * @param  [type] $problem_id [问题ID]
	 * @param  [type] $type       [类型 0：补充问题查询 1：指令记录查询 2：协同记录查询]
	 * @return [type]             [description]
	 */
	public function getQusetionDetails($problem_id, $type)
	{
		if(!$problem_id) {
			$this->setError('问题id不能为空');
			return false;
		}
		if($type == 0) {
			$field = ['question_content', 'author', 'group', 'create_time'];
			$result = Question::find()
					->select($field)
					->where(['problem_id' => $problem_id])
					->orderBy('create_time desc')
					->asArray()
					->all();
			
		} else if ($type == 1) {
			$field = ['instruction_content', 'author', 'group', 'create_time'];
			$result = Instruction::find()
					->select($field)
					->where(['problem_id' => $problem_id])
					->orderBy('create_time desc')
					->asArray()
					->all();
			
		} else if ($type == 2) {
			$field = ['collaboration_content', 'author', 'group', 'create_time'];
			$result = Collaboration::find()
					->select($field)
					->where(['problem_id' => $problem_id])
					->orderBy('create_time desc')
					->asArray()
					->all();
			
		}
		if(!$result) {
				$this->setError('暂无该记录');
				return false;
			}

		for($i = 0; $i < count($result); $i ++ ){
			if($result[$i]['group'] == '0') {
				$result[$i]['group'] = '';
			}
		}
		return $result;
	} 
}