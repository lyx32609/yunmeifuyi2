<?php
namespace app\services;

use app\foundation\Service;
use app\models\CompanyStatus;
class GetStatusService extends Service
{
	public function getStatus()
	{
		/**
		 * 获取企业类型
		 * @var [type]
		 */
		$result = CompanyStatus::find()
				->select(['status', 'name'])
				->asArray()
				->all();
		if(!$result){
			$this->setError('企业信息不存在');
			return false;
		}
		return $result;
	}
}