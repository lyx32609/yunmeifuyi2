<?php
namespace app\services;

use Yii;
use app\foundation\Service;
use app\benben\NetworkHelper;
use app\services\HttpCurlService;
use app\models\CompanyInterface;
class GetGoodsListService extends Service
{
	private $goods_api = 'goods/getGoodsSpec'; //获取商品规格接口地址
	private $bar_cord = 'goods/getGoodsBarCord'; //根据条形码和采购商 获取商品信息
	private $goods_member = 'goods/getSupplierMessage'; //获取供应商接口
	private $basic_member = 'basic/getSupplier'; //获取供应商接口 信息更加详细
	private $supplier_api = 'goods/addGoodsToCart'; //加入购物车接口
	private $addGoods_api = 'goods/addGoodsToCart'; //生成订单接口
	/**
	 * [getGoodsBarCord description]
	 * @param  [type] $barCord  条形码
	 * @param  [type] $memberId 采购商ID
	 * @return [type]           [description]
	 */
	public function getGoodsBarCord($barCord, $memberId)
	{
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
		    //print_r($result['result']);exit();
		    $result = $result['result'];
            foreach ($result as $k => &$v){
                $rsdata[$k]['company_id'] = $v['company_id'];
                $rsdata[$k]['goods_id'] = $v['goods_id'];
                $rsdata[$k]['barcode'] = $v['barcode'];
                $rsdata[$k]['member_id'] = $memberId;
                $supplier['supplierId'] = $v['company_id'];
                //调取供货商信息接口 
                $cart = \Yii::$app->api->request($this->basic_member,$supplier);
                if($cart['ret'] === 0) {
                    $rsdata[$k]['company_name'] = $cart[0]['company_name'];
                    $rsdata[$k]['company_address'] = $cart[0]['address'];
                } else {
                    $rsdata[$k]['company_name'] = '{}';
                    $rsdata[$k]['company_address'] = '{}';
                }
            }
            return $rsdata;
		
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
	/**
	 * [getGoodsBarCord description](改版后)
	 * @param  [type]              $barCord  条形码
	 * @param  [type]              $memberId 采购商ID
	 * @return [type]              [description]
	 */
	public function getGoodsBarCordNew($barCord, $memberId, $is_cooperation, $company_categroy_id)
	{
	    if(!$barCord) {
	
	        $this->setError('条形码不能为空');
	        return false;
	    }
	    if(!$memberId) {
	        $this->setError('采购商不能为空');
	        return false;
	    }
	    if($is_cooperation == 0){
	        $url =  CompanyInterface::find()
                ->select(['url', 'public_key', 'privace_key', 'protocol'])
                ->where(['company_id' => $company_categroy_id])
                ->andWhere(['module_id' => 15])
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
	            $this->setError('暂无供货商信息');
	            return false;
	        }
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
	        //print_r($result['result']);exit();
	        $result = $result['result'];
	        foreach ($result as $k => &$v){
	            $rsdata[$k]['company_id'] = $v['company_id'];
	            $rsdata[$k]['goods_id'] = $v['goods_id'];
	            $rsdata[$k]['barcode'] = $v['barcode'];
	            $rsdata[$k]['member_id'] = $memberId;
	            $supplier['supplierId'] = $v['company_id'];
	            //调取供货商信息接口
	            $cart = \Yii::$app->api->request($this->basic_member,$supplier);
	            if($cart['ret'] === 0) {
	                $rsdata[$k]['company_name'] = $cart[0]['company_name'];
	                $rsdata[$k]['company_address'] = $cart[0]['address'];
	            } else {
	                $rsdata[$k]['company_name'] = '{}';
	                $rsdata[$k]['company_address'] = '{}';
	            }
	        }
	        return $rsdata;
	
	    }else {
	        $this->setError('暂无该商品信息');
	        return false;
	    }
	}
}

