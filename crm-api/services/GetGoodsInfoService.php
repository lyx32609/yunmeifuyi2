<?php
namespace app\services;

use Yii;
use app\foundation\Service;
use app\benben\NetworkHelper;

class GetGoodsInfoService extends Service
{
	private $goods_api = 'goods/getGoodsSpec'; //获取商品规格接口地址
	private $bar_cord = 'goods/getGoodsBarCord'; //根据条形码和采购商 获取商品信息
	private $goods_member = 'goods/getSupplierMessage'; //获取供应商接口
	private $supplier_api = 'goods/addGoodsToCart'; //加入购物车接口
	private $addGoods_api = 'goods/addGoodsToCart'; //生成订单接口
	/**
	 * [getGoodsBarCord description]
	 * @param  [type] $barCord  条形码
	 * @param  [type] $memberId 采购商ID
	 * @return [type]           [description]
	 */
	public function getGoodsBarCord($barCord, $memberId){
		if(!$barCord) {

			$this->setError('条形码不能为空');
			return false;
		}
		if(!$memberId) {
			$this->setError('采购商不能为空');
			return false;
		}
		$data['barCord'] = $barCord;
		$data['memberId'] = $memberId;
		
		/**
		 * 调商品信息接口
		 */
		$result = \Yii::$app->api->request($this->bar_cord,$data);
		if($result['ret'] === 100) {
			return $result;
		}
		
		if($result['result'] !== "fasle") {


			$result = $result['result'][0];
			if(is_string($result['big_pic'])) {
				$image = explode('|', $result['big_pic']);
				unset($result['big_pic']);
				$result['big_pic'] = $image[0];
			}
			
			$goodSpec = [
				'memberId' => $memberId, 
				'goodsId' => $result['goods_id']
			];
			/**
			 * 调取商品规格接口
			 */
			//var_dump($goodSpec);exit;
 			$spec = \Yii::$app->api->request($this->goods_api, $goodSpec);
 			
  			if($spec['ret'] === 0) {
 				unset($spec['ret']);
 				$result['spec'] = $spec['result'];
 			} else {
 				$result['spec'] = '{}'; 
 			}

 			$supplier['supplierId'] = $result['company_id'];
 			
 			/**
 			 * 调取供货商信息接口
 			 */
 			$cart = \Yii::$app->api->request($this->goods_member,$supplier);
 			
 			if($cart['ret'] === 0) {
 				
 				$result['company_name'] = $cart['result'][0]['company_name']; 
 			} else {
 				$result['company_name'] = '{}'; 
 			} 
 			return $result;
		
		}else {
			$this->setError('暂无该商品信息');
			return false;
		}
	}
	/**
	 * 生成订单
	 * @param [type] $goodsId  商品ID
	 * @param [type] $memberId 采购商ID
	 * @param [type] $num      数量
	 */
	public function addGoods($goodsId, $memberId, $num)
	{
		if(!$goodsId) {
			$this->setError('商品ID不能为空');
			return false;
		}
		if(!$memberId) {
			$this->setError('店铺ID不能为空');
			return false;
		}
		if(!$num) {
			$this->setError('购买数量不能为空');
			return false;
		}
		$data['goodsId'] = $goodsId;
		$data['memberId'] = $memberId;
		$data['num'] = $num;
		
		$result = \Yii::$app->api->request($this->addGoods_api, $data);
		
		
		if($result['ret'] == 0) {
			return $result =  '添加购物车成功';
		} 
		$this->setError('添加购物车失败');
		return false;
	}
}

