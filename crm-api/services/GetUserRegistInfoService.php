<?php
namespace app\services;

use app\foundation\Service;
use app\models\User;
class GetUserRegistInfoService extends Service
{
	/**
	 * 获取用户注册量
	 * @param  [type] $start_time              [开始时间]
	 * @param  [type] $end_time              [结束时间]
	 */
	public function showUserInfo($start_time,$end_time)
	{
		if(!$start_time)
		{
			$start = mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
		}
		else
		{
			$start =   strtotime($start_time);
		}
		if(!$end_time)
		{
			$end = mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y')) ;
		}
		else
		{
			$end = strtotime($end_time);
		}
		$total = User::find()->select(['count(id) as num'])->asArray()->one();
		$weekTotal = User::find()->select(['count(id) as num'])->where(['between','create_time',$start,$end])->asArray()->one();
		$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y-%m-%d') AS dates,COUNT(id) AS registnum FROM off_user WHERE create_time >= $start AND create_time <= $end GROUP BY dates";
		$data = User::findBySql($sql)->asArray()->all();
		if($data)
		{
			$return_data['RegisteredTotal'] = $total['num'];
			$return_data['WeekTotal'] = $weekTotal['num'];
			$return_data['Items'] = $data;
			return $result = [
				'isSuccess'=>1,
			    'message' => '请求成功',
			    'data' => $return_data,
			];
		}
		else
		{
            $this->setError('暂无数据');
            return false;
        }
	}

	/*展示企业信息*/
	public function showCompanyInfo($companyId)
	{
		if(!$companyId)
		{
			$this->setError('企业ID不能为空');
			return false;
		}
		$companyInfo = CompanyCategroy::find()
					->select(["off_user.username","off_user.phone","off_company_categroy.*","off_company_goods.goods_name","off_company_service.service_name","off_company_product.product_name"])
					->where(["off_company_categroy.id"=>$companyId])
					->leftJoin('off_company_goods', 'off_company_categroy.goods_type = off_company_goods.id')
					->leftJoin('off_company_service','off_company_categroy.service_type = off_company_service.id')
					->leftJoin('off_company_product','off_company_categroy.product_type = off_company_product.id')
					->leftJoin('off_user','off_company_categroy.id = off_user.company_categroy_id')
					->asArray()
					->one();
		return $companyInfo;

	}
}
