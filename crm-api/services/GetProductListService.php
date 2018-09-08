<?php
namespace app\services;

use app\foundation\Service;
use app\models\CompanyProduct;
use app\models\CompanyGoods;
use app\models\CompanyService;
class GetProductListService extends Service
{
	/**
	 * 获取产品类型
	 * @return [type] [description]
	 */
	public function getProductList()
	{
		$result = CompanyProduct::find()
				->select(['id', 'product_name'])
				->asArray()
				->all();
		return $result;
	}
	/**
	 * 获取服务类型
	 * @return [type] [description]
	 */
	public function getServiceList()
	{
		$result = CompanyService::find()
				->select(['id', 'service_name'])
				->asArray()
				->all();
		return $result;
	}
	/**
	 * 获取商品
	 * @return [type] [description]
	 */
	public function getGoodsList()
	{
		$result = CompanyGoods::find()
				->select(['id', 'goods_name'])
				->asArray()
				->all();
		return $result;
	}
}