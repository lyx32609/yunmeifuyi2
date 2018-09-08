<?php
namespace app\services;

use Yii;
use app\foundation\Service;
use app\benben\NetworkHelper;
use app\models\CompanyInterface;
use app\services\HttpCurlService;
use app\models\Goods;
use app\models\User;
class GetGoodsInfoNewService extends Service
{
	private $goods_api = 'goods/getGoodsSpec'; //获取商品规格接口地址
	private $bar_cord = 'goods/getGoodsBarCord'; //根据条形码和采购商 获取商品信息
	private $goods_member = 'goods/getSupplierMessage'; //获取供应商接口
	private $basic_member = 'basic/getSupplier'; //获取供应商接口 信息更加详细
	private $supplier_api = 'goods/addGoodsToCart'; //加入购物车接口
	// private $addGoods_api = 'goods/addGoodsToCart'; //生成订单接口
	private $addGoods_api = 'cart/barcodeToCart';
	/**
	 * [getGoodsBarCord description]
	 * @param  [type]          $barCord  条形码
	 * @param  [type]          $memberId 采购商ID
	 * @param  [type]          $companyId 供货商ID
	 * @return [type]          [description]
	 */
	public function getGoodsBarCord($barCord, $memberId, $companyId, $is_cooperation, $company_categroy_id)
	{
		if(!$barCord) {
			$this->setError('条形码不能为空');
			return false;
		}
		if(!$memberId) {
			$this->setError('采购商不能为空');
			return false;
		}
		if(!$companyId) {
			$this->setError('供货商不能为空');
			return false;
		}
		$data['barCord'] = $barCord;
		$data['memberId'] = $memberId;
		
		/**
		 * 调商品信息接口
		 */
		if($is_cooperation == 0){
		    $url = CompanyInterface::find()
                ->select(['url', 'public_key', 'privace_key', 'protocol'])
                ->where(['company_id' => $company_categroy_id])
                ->andWhere(['module_id' => 1])
                ->asArray()
                ->one();
		    if(!$url){
		        $this->setError('请先添加接口');
		        return false;
		    }
		    $param = [
		        'barCord' => $barCord
		    ];
		    $http = HttpCurlService::instance();
		    $result = $http->request($url['url'], $url['public_key'] . $url['privace_key'], $param, $url['protocol']);
		    if($result){
                return $result;
            } else {
                $this->setError('暂无商品信息');
                return false;
            }
		}
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

 			$supplier['supplierId'] = $companyId;
 			
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
	public function addGoods($barcode,$goodsId, $memberId, $num, $user_id, $company_id, $goods_company, $goods_name, $is_cooperation, $orders_money)
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
		if(!$user_id) {
		    $this->setError('用户id不能为空');
		    return false;
		}
		if(!$company_id) {
		    $this->setError('公司id不能为空');
		    return false;
		}
		if(!$goods_company) {
			$this->setError('供应商不能为空');
			return false;
		}
		if(!$goods_name) {
		    $this->setError('商品名称不能为空');
		    return false;
		}
		if(!$orders_money){
		    $this->setError('订单金额不能为空');
		    return false;
		}
		$user = User::findOne(['id' => $user_id]);
		if(!$user){
		    $this->setError('用户不存在');
		    return false;
		}
		if($is_cooperation == 0){
		    $url = CompanyInterface::find()
                ->select(['url', 'public_key', 'privace_key', 'protocol'])
                ->where(['company_id' => $company_id])
                ->andWhere(['module_id' => 2])
                ->asArray()
                ->one();
		    if(!$url){
		        $this->setError('请先添加对接接口');
		        return false;
		    }
		    $param = [
		        'member_id' => $memberId,
		        'goods_id' => $goodsId,
		        'goods_num' => $num
		    ];
		    $http = HttpCurlService::instance();
		    $result = $http->request($url['url'], $url['public_key'] . $url['privace_key'], $param, $url['protocol']);
		    if($result){
		        $data = new Goods();
		        $data->user_id = $user_id;
		        $data->user_name = $user->username;
		        $data->company_id = $company_id;
		        $data->orders_money = $orders_money;
		        $data->goods_id = $goodsId;
		        $data->goods_name = $goods_name;
		        $data->goods_company = $goods_company;
		        $data->goods_num = $num;
		        $data->createtime = time();
		        if(!$data->save()){
		            var_dump($data->getErrors());exit;
		            $this->setError('订单存储失败');
		            return false;
		        }
		        return $result = '添加购物车成功';
		    } else {
		        $this->setError('添加购物车失败');
		        return false;
		    }
		}
		// $data['goodsId'] = $goodsId;
		$arr['barcode'] = $barcode;
		$arr['num'] = $num;
		$arr['shopname'] = $goods_company;
		$data[] = $arr;

		// $result = \Yii::$app->api->request($this->addGoods_api, $data);
		$result = \Yii::$app->api->request($this->addGoods_api,['member_id'=>$memberId,'data'=> json_encode($data)]);
		
		
		if($result['ret'] == 0) {
			return $result =  '添加购物车成功';
		} 
		$this->setError('添加购物车失败');
		return false;
	}
}

