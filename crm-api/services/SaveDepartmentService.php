<?php

namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\CompanyCategroy;

class SaveDepartmentService extends Service
{
	public function saveDepartment($name,$is_select,$childs,$companyId,$domain_id,$id)
	{
		/*查看是否有权限start*/   
		if(!$companyId){
			$this->setError('企业ID不能为空');
			return false;
		}
		$company_data = CompanyCategroy::find()
					  ->select(["id","createtime","failure"])
					  ->where(["id"=>$companyId])
					  ->one();
		$failure = $company_data['failure'];
		$createtime = $company_data['createtime'];
		$time = floor((time()-$createtime)/3600/24);
		if($failure == 1 && $time > 10)//超过试用期或者没有权限
		{
			$this->setError('暂无权限或已到期！');
			return false;		
		}
		/*查看是否有权限end*/
		if(!$id)//新增
		{
				if(!$name){
					$this->setError('部门名字不能为空');
					return false;
				}
				if($is_select == ""){
					$this->setError('是否统计不能为空');
					return false;
				}
				if($domain_id == ""){
					$this->setError('区域ID不能为空');
					return false;
				}
				$data = UserDepartment::find()
						->select(["id","parent_id"])
						->where(['name' => $name])
						->andWhere(['company' => $companyId])
						->andWhere(["is_show"=>1])
						->asArray()
						->one();

				//return $data;
				if($data)//部门存在
				{
					// $parent_id = $data['id'];
					// if(!$childs)
					// {
						$this->setError('部门已存在');
						return false;
					// }
					// else//部门存在 子部门也不能添加
					// {
					// 	$child_arr = explode(",",$childs);
					// 	foreach($child_arr as $v)
					// 	{
					// 		$data_child = UserDepartment::find()
					// 					->where(['name' => $v])
					// 					->andWhere(["is_show"=>1])
					// 					->andWhere(["parent_id"=>$parent_id])
					// 					->asArray()
					// 					->one();
					// 		if($data_child){
					// 			$this->setError('子部门已存在');
					// 			return false;
					// 		}
					// 		else
					// 		{
					// 			$department_child = new UserDepartment;
					// 			$department_child->name = $v;
					// 			$department_child->parent_id = $data['id'];
					// 			$department_child->is_select = $is_select;
					// 			$department_child->domain_id = $domain_id;
					// 			$department_child->company = $companyId;
					// 			$result_child = $department_child->save();
					// 		}
					// 	}
					// }
				}
				else//部门不存在
				{
					$department = new UserDepartment;
					$department->name = $name;
					$department->parent_id = 0;
					$department->is_select = $is_select;
					$department->domain_id = $domain_id;
					$department->company = $companyId;
					$result = $department->save();
					$data = UserDepartment::find()
							->select(["id","parent_id"])
							->where(['name' => $name])
							->andWhere(["is_show"=>1])
							->andWhere(['company' => $companyId])
							->asArray()
							->one();
					$parent_id = $data['id'];
					if($childs)//保存子部门
					{
							 $child_arr = explode(",",$childs);
							foreach($child_arr as $v)
							{
								$data_child = UserDepartment::find()
											->where(['name' => $v])
											->andWhere(["is_show"=>1])
											->andWhere(["parent_id"=>$parent_id])
											->andWhere(['company' => $companyId])
											->asArray()
											->one();
								if($data_child){
									$this->setError('子部门已存在');
									return false;
								}
								else
								{
									$department_child = new UserDepartment;
									$department_child->name = $v;
									$department_child->parent_id = $parent_id;
									$department_child->is_select = $is_select;
									$department_child->domain_id = $domain_id;
									$department_child->company = $companyId;
									$result_child = $department_child->save();
								}
							}
					}
				}

			if($result_child || $result){
				 $sss['msg'] = '添加成功';
				 $sss['depart_id'] = $data['id'];
				 return $sss;
			}
			else
			{
				$this->setError('添加失败');
				return false;
			}
		}
		else
		{//修改
				if(!$name){
					$this->setError('部门名字不能为空');
					return false;
				}
				if($is_select == ""){
					$this->setError('是否统计不能为空');
					return false;
				}
				if($domain_id == ""){
					$this->setError('区域ID不能为空');
					return false;
				}
				$data = UserDepartment::find()
						->select(["id","parent_id"])
						->where(['name' => $name])
						->andWhere(['company' => $companyId])
						->andWhere(["is_show"=>1])
						->asArray      ()
						->one();
				$parent_id = $data['parent_id'];
				if($data)//部门存在
				{
					/*将原来子部门清空start*/
					$department_data = UserDepartment::find()
									->where(["parent_id"=>$id])
									->andWhere(["is_show"=>1])
									->asArray()
									->all();
					$department = UserDepartment::find()->where(['id' => $id])->one();
					$department->is_select = $is_select;
					$result = $department->save();
					foreach($department_data as $v)
					{
						$child_id = $v['id'];
						$department = UserDepartment::find()->where(['id' => $child_id])->one();
						$department->is_show = 0;
						$result = $department->save();
					}
					/*将原来子部门清空end*/
					//保存子部门
					$child_arr = explode(",",$childs);
					foreach($child_arr as $v)
					{
						$data_child = UserDepartment::find()
									->where(['name' => $v])
									->andWhere(["is_show"=>1])
									->andWhere(["parent_id"=>$id])
									->andWhere(['company' => $companyId])
									->asArray()
									->one();
									//return $data_child;
						if($data_child)
						{
							$this->setError('子部门已存在');
							return false;
						}
						else
						{
							$department_child =  new UserDepartment;
							$department_child->name = $v;
							$department_child->parent_id = $id;
							$department_child->is_select = $is_select;
							$department_child->domain_id = $domain_id;
							$department_child->company = $companyId;
							$result_child = $department_child->save();
						}
					}
				}
				else//部门不存在
				{
					$department =  UserDepartment::find()->where(["id"=>$id])->one();
					$department->name = $name;
					$department->parent_id = 0;
					$department->is_select = $is_select;
					$department->domain_id = $domain_id;
					$department->company = $companyId;
					$result = $department->save();
					/*将原来子部门清空start*/
					$department_data = UserDepartment::find()
									->where(["parent_id"=>$id])
									->andWhere(["is_show"=>1])
									->asArray()
									->all();
					foreach($department_data as $v)
					{
						$child_id = $v['id'];
						$department = UserDepartment::find()->where(['id' => $child_id])->one();
						$department->is_show = 0;
						$department->save();
					}
					/*将原来子部门清空end*/
						//保存子部门
					$child_arr = explode(",",$childs);
					foreach($child_arr as $v)
					{
						$data_child = UserDepartment::find()
									->where(['name' => $v])
									->andWhere(["is_show"=>1])
									->andWhere(['company' => $companyId])
									->andWhere(["parent_id"=>$id])
									->asArray()
									->one();
						if($data_child)
						{
							$this->setError('子部门已存在');
							return false;
						}
						else
						{
							$department_child =  new UserDepartment;
							$department_child->name = $v;
							$department_child->parent_id = $id;
							$department_child->is_select = $is_select;
							$department_child->domain_id = $domain_id;
							$department_child->company = $companyId;
							$result_child = $department_child->save();
						}
					}
				}
			if($result || $result_child)
			{
				return $result = '修改成功';
			}
			else
			{
				$this->setError('修改失败');
				return false;
			}
		}
	}
	/*删除部门及子部门*/
	public function delDepartment($companyId,$id)
	{
		/*查看是否有权限start*/   
		if(!$companyId){
			$this->setError('企业ID不能为空');
			return false;
		}
		$company_data = CompanyCategroy::find()
					  ->select(["id","createtime","failure"])
					  ->where(["id"=>$companyId])
					  ->one();
		$failure = $company_data['failure'];
		$createtime = $company_data['createtime'];
		$time = floor((time()-$createtime)/3600/24);
		if($failure == 1 && $time > 10)//超过试用期或者没有权限
		{
			$this->setError('暂无权限或已到期！');
			return false;		
		}
		/*查看是否有权限end*/
		if($id)
		{
			$department =  UserDepartment::findOne($id);
			$department->is_show = 0;
			if($department['parent_id'] == 0)//主体部门
			{
				$department_child = UserDepartment::find()
								->where(["parent_id" => $id])
								->andWhere(["is_show"=>1])
								->asArray()
								->all();
				if($department_child)//将子部门全部隐藏
				{
					foreach($department_child as $v)
					{
						$depart_child =  UserDepartment::findOne($v['id']);
						$depart_child->is_show = 0;
						$result_child = $depart_child->save();
					}
				}
			}
			$result = $department->save();
			if($result || $result_child)
			{
				return $result = '删除成功';
			}
			else
			{
				return $result = '删除失败';
			}
		}
	}

	/*子部门修改*/
	public function editChildDepart($depart_id,$companyId,$name)
	{
		if(!$name){
			$this->setError('部门名字不能为空');
			return false;
		}
		/*查看是否有权限start*/   
		if(!$companyId){
			$this->setError('企业ID不能为空');
			return false;
		}
		$company_data = CompanyCategroy::find()
					  ->select(["id","createtime","failure"])
					  ->where(["id"=>$companyId])
					  ->one();
		$failure = $company_data['failure'];
		$createtime = $company_data['createtime'];
		$time = floor((time()-$createtime)/3600/24);
		if($failure == 1 && $time > 10)//超过试用期或者没有权限
		{
			$this->setError('暂无权限或已到期！');
			return false;		
		}
		/*查看是否有权限end*/
		$data = UserDepartment::find()
				->select(["id","parent_id"])
				->where(['name' => $name])
				->andWhere(["is_show"=>1])
				->one();
		return $data;
		if(!$data)
		{
			$department =  UserDepartment::find()->where(["id"=>$depart_id])->one();
			$department->name = $name;
			if($department->save())
			{
				return $result = '修改子部门成功';
			}
			else
			{
				$this->setError('修改子部门失败');
				return false;
			}
		}
		else
		{
				$this->setError('该子部门已存在');
				return false;
		}

	}
}