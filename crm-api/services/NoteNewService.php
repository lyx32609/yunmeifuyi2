<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserLocation;
use app\benben\DateHelper;
use app\models\ShopNote;
use app\models\UserBusiness;
use app\models\UserBusinessNotes;
use app\models\User;
use app\models\Shop;

/**
 * @author Pulo
 *
 */
class NoteNewService extends Service
{
	
	/**
     * @param unknown $shopId 商家id   2.1版本添加接口  添加字段是否合理 reasonable
     * @param unknown $content 备注内容
     * @param int $shopid 店铺ID text $conte：提交的内容  varchar $user：提交人   decimal $longitude：提交人经度    decimal $latitude：提交人纬度    varchar imag：{http：\\...,第二张，第三张}
     */
    public function addReasonable($shopId, $conte, $user, $longitude, $latitude, $imag, $belong, $is_cooperation)
    {
    	if(!$shopId){
    		$this->setError('店铺id不为空!');
    		return false;
    	}
    	if(!$conte){
    		$this->setError('内容不能为空!');
    		return false;
    	}
    	if(!$longitude){
    		$this->setError('经度不能为空!');
    		return false;
    	}
    	if(!$latitude){
    		$this->setError('纬度不能为空!');
    		return false;
    	}

    	$columns = array(
    			'shop_id'=>$shopId,
    			'note' => $conte,
    			'time'=>time(),
    			'conte' => $conte,
    			'user' => $user,
    			'longitude' => $longitude,
    			'latitude' => $latitude,
    			'imag' => $imag,
    			'belong' => $belong,
    	);
    	if($is_cooperation == 0) {
    	    $rs = \Yii::$app->dbofficial->createCommand()->insert('off_company_shop_note', $columns)->execute();
    	} else {
    	    $rs = \Yii::$app->dbofficial->createCommand()->insert('off_shop_note', $columns)->execute();
    	}
    	if(!$rs)
    	{
    		$this->setError('添加失败!');
    		return false;
    	}
    	else
    	{
    		if($is_cooperation == 0){
    			$shop = Shop::find()
    					->select(['id','shop_name', 'shop_longitude', 'shop_latitude', 'shop_domain'])
    					->where(['id' => $shopId])
    					->asArray()
    					->one();
    			if(!$shop){
    				$this->setError('店铺不存在');
    				return false;
    			}
    			$user_location = new UserLocation();
				$user_location->shop_id = $shopId;
				$user_location->name = isset($shop['shop_name']) ? $shop['shop_name'] : '';
				$user_location->longitude = $shop['shop_longitude'];
				$user_location->latitude = $shop['shop_latitude'];
				$user_location->user_longitude = $longitude;
				$user_location->user_latitude = $latitude;
				$user_location->user = $user;
				$user_location->time = time();
				$user_location->type = 0;
				$user_location->domain = $shop['shop_domain'];
				$user_location->belong = $belong;
				//计算定位是否合理 俩点之间的经纬度距离小于80M为合理
	    		$point1 = array('lat' => $shop['shop_latitude'], 'long' => $shop['shop_longitude']);
	    		$point2 = array('lat' => $latitude, 'long' => $longitude);
	    		$distance = $this->getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
	    		if($distance < 80){
	    		    $user_location->reasonable = '合理';
	    		}else{
	    		    $user_location->reasonable = '不合理';
	    		}
	    		
	    		$username = User::find()
	    				->select('name')
	    				->where(['username'=>$user])
	    				->asArray()
	    				->one();
	    	    $user_location->username = $username['name'];
	    	    if (!$user_location->save())
	    		{
	    			$this->setError('添加失败!');
	    			return false;
	    		}
	    		return true;

    		}
    		if($belong == '1')
    		{
    			$shop = \Yii::$app->api->request('basic/getMember',['member_id'=>$shopId]);
    			 
    			if(!$shop)
    			{
    				$this->setError('采购商信息错误');
    				return false;
    			}
    			if($shop['ret']===0)
    			{
    				$name = $shop[0]['shopname'];
    			}else{
    				$this->setError('接口返回错误：'. $shop['msg']);
    				return false;
    			}
    			 
    		}elseif($belong == '2')
    		{
    			$supplier = \Yii::$app->api->request('basic/getSupplier',['supplierId'=>$shopId]);
    			if(!$supplier)
    			{
    				$this->setError('供货商信息错误');
    				return false;
    			}
    			if($supplier['ret']===0)
    			{
    				$name = $supplier[0]['company_name'];
    			}else{
    				$this->setError('接口返回错误：'. $supplier['msg']);
    				return false;
    			}
    		}
    		$user_domain_id = User::findOne(['username' => $user]);
    		$user_location = new UserLocation();
    		$user_location->shop_id = $shopId;
    		$user_location->name = isset($name) ? $name : '';
    		$user_location->longitude = $longitude;
    		$user_location->latitude = $latitude;
    		$user_location->user_longitude = $longitude;
    		$user_location->user_latitude = $latitude;
    		$user_location->user = $user;
    		$user_location->time = time();
    		$user_location->type = 0;
    		$user_location->domain = intval($user_domain_id->domain_id);
    		$user_location->belong = $belong;
    		//计算定位是否合理 俩点之间的经纬度距离小于80M为合理
    		$point1 = array('lat' => $shop[0]['latitude'], 'long' => $shop[0]['longitude']);
    		$point2 = array('lat' => $latitude, 'long' => $longitude);
    		$distance = $this->getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
    		if($distance < 80){
    		    $user_location->reasonable = '合理';
    		}else{
    		    $user_location->reasonable = '不合理';
    		}
    		
    		$username = User::find()->select('name')->where(['username'=>$user])->asArray()->one();
    		//print_r($username);exit();
    	   $user_location->username = $username['name'];
    		
    	//	print_r($user_location);exit();
/*     		$shop_longitude = sprintf("%.3f",substr(sprintf("%.4f", $shop[0]['longitude']), 0,-1));
    		$shop_latitude =  sprintf("%.3f",substr(sprintf("%.4f", $shop[0]['latitude']), 0,-1));
    		$res_longitude = sprintf("%.3f",substr(sprintf("%.4f", $longitude), 0,-1));
    		$res_latitude = sprintf("%.3f",substr(sprintf("%.4f", $latitude), 0,-1));
    		if ((abs($shop_longitude-$res_longitude))>0||(abs($shop_latitude-$res_latitude))>0){
    			$user_location->reasonable = 2;
    		}else {
    			$user_location->reasonable = 1;
    		} */
    		
    		if (!$user_location->save())
    		{var_dump($user_location->getErrors());exit;
    			$this->setError('添加失败!');
    			return false;
    		}
    		 
    	}
    	return true;
    }
    //获取两个经纬度之间的距离
    public function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return $meters;
    }
}