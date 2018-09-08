<?php
/*created by 付腊梅 2017-04-26*/
namespace app\services;

use app\foundation\Service;
use app\models\UserDepartment;
use app\models\CompanyCategroy;
class GetAllDepartmentListService extends Service
{
	/**
	 * 根据省市获取部门及子部门列表
	 */
	public function getDepartmentList($companyId)
	{
		//先判断是否试用
		$company_data = CompanyCategroy::find()
				->select(["id","createtime","failure"])
				->where(["id"=>$companyId])
				->asArray()
				->one();//查询企业信息
		$failure = $company_data['failure'];
		$createtime = $company_data['createtime'];
		$time = floor((time()-$createtime)/3600/24);
		if(($failure == 1 && $time < 11) || ($failure == 0))//永久使用或在试用期内
		{
			$department_data = UserDepartment::find()
					->select(["id","parent_id","name","company","is_select"])
					->where(["company"=>$companyId])
					->andWhere(["parent_id"=>0])
					->andWhere(["is_show"=>1])
					->orderBy('id desc')
					->asArray()
					->all();//查询部门数据
			if(!$department_data)
			{
				$this->setError('暂无部门');
				return false;
			}
			else
			{
				//$result = array();
				foreach($department_data as $k=>$v)
				{
					if($v['parent_id'] == 0)//查看是否有子部门
					{
						$childs = UserDepartment::find()
								->select(["id","parent_id","name",'is_select'])
								->where(["parent_id"=>$v['id']])
								->andWhere(["is_show"=>1])
								->orderBy('id desc')
								->asArray()
								->all();//获取子部门数据
						
						if($childs)
						{
							$department_data[$k]['childs'] = $childs;
						}
						else
						{
							$department_data[$k]['childs'] = array();
						}

					}
				}
				return $department_data;
			}
		}
		else
		{
			$this->setError('暂无权限或已到期！');
			return false;
		}
	}
}