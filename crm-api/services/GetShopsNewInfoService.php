<?php
namespace app\services;

use app\foundation\Service;
use app\models\Order;
use app\models\Member;
use app\benben\DateHelper;
use app\models\ShopNote;
use yii\data\Pagination;
use app\models\UserSupplier;
use app\models\Users;
use app\models\UserDomain;
use app\models\User;
use app\models\Shop;
use app\models\UserBusiness;

class GetShopsNewInfoService extends Service
{
	private  $getShop = 'shop/getByPhone';
    /**
     * 根据名称模糊查询店铺集合
     * @param  [type]  $shopName            [店铺名称]
     * @param  [type]  $type                [0店铺 1供货商]
     * @param  integer $page                [分页值]
     * @param  integer $pageSize            [分页数量]
     * @param  string  $join                [1已和集采合作 2未和集采合作]
     * @param  [type]  $user_id             [登录人id]
     * @param  [type]  $is_cooperation      [登录人公司是否与云媒合作]
     * @param  [type]  $company_category_id [登陆人所在公司id]
     * @return [type]                       [description]
     */
    public function getShopsName($shopName, $type, $page=1, $pageSize=10, $join='', $user_id, $is_cooperation, $company_category_id)
    {
        if(!$shopName)
        {
            $this->setError('请输入名称!');
            return false;
        }
        if(!$type)
        {
            $type = 0;
        }
        $user = User::findOne(['id' => $user_id]);
        if($is_cooperation == 0){
            if($type == '0'){
                $type = 3;
            } else if($type == '1'){
                $type = 2;
            } else if($type == '2'){
                $type = 1;
            }
        	$data = $this->getLocationShops($shopName, $user->domain_id, $company_category_id, $page, $type, $pageSize, $join);
            if(!$data){
            	$this->setError('暂无数据');
            	return false;
            }
            if(($type == '3' && $join =='1') || ($type == '3' && $join =='2') ) {
                for($i = 0; $i < count($data); $i++){
                    $result[$i]['member_id'] = $data[$i]['id'];
                    $result[$i]['check_status'] = $data[$i]['shop_review'];
                    $result[$i]['shopname'] = $data[$i]['shop_name'];
                    $result[$i]['addr'] = $data[$i]['shop_addr'];
                    $result[$i]['uname'] = '';
                    $result[$i]['mobile'] = $data[$i]['phone'];
                    $result[$i]['staff_code'] = $data[$i]['user_name'];
                    $result[$i]['longitude'] = $data[$i]['shop_longitude'];
                    $result[$i]['latitude'] = $data[$i]['shop_latitude'];
                }
            } else {
                for($i = 0; $i < count($data); $i++){
                    $result[$i]['uid'] = $data[$i]['id'];
                    $result[$i]['check_status'] = $data[$i]['shop_review'] == 2 ? 1 : 0;
                    $result[$i]['company_name'] = $data[$i]['shop_name'];
                    $result[$i]['address'] = $data[$i]['shop_addr'];
                    $result[$i]['username'] = '';
                    $result[$i]['linkman_tel'] = $data[$i]['phone'];
                    $result[$i]['linkman'] = $data[$i]['user_name'];
                    $result[$i]['longitude'] = $data[$i]['shop_longitude'];
                    $result[$i]['latitude'] = $data[$i]['shop_latitude'];
                }
            }
            
            return $list = [
            	'list' => $result,
            	'pageCount' => count($result),
            	'type' => $type == 3 ? 0 : 1,
            	'realPage' => $page
            ];
        }
        $domain = UserDomain::find()
        		->select('domain_id')
        		->where(['are_region_id' => $user->domain_id])
        		->asArray()
        		->one();
        $result = \Yii::$app->api->request('shop/getShops',[
            'shopName' => $shopName,
            'type' => $type,
            'page' => $page,
            'pageSize' => $pageSize,
            'join' => $join,
            'domain' => $domain['domain_id'],
        ]);
        if($result['ret'] === 0)
        {
            return $result['result'];
        }else{
            $this->setError('暂无数据');
            return false;
            if($type == 0){
                $type = 5;
            } else if($type == 1){
                $type = 2;
            }
            $result = $this->getBusinessList($shopName, $user->domain_id, $page, $type, $pageSize);
            if(!$result){
                $this->setError('暂无数据');
                return false;
            }
            return [
                'ret' => 28,
                'result' => $shopName
            ];
        }
    }
    /**
     * 查询本地店铺集合(查询注册企业)
     * @param  [type] $domain_id           [description]
     * @param  [type] $company_category_id [description]
     * @param  [type] $page                [description]
     * @param  [type] $type                [description]
     * @return [type]                      [description]
     */
    public function getLocationShops($shop_name, $domain_id, $company_category_id, $page, $type, $pageSize, $join)
    {
        if($join == 1){
            $join = 3;
        }
        if($join == '3'){
            $shops = Shop::find()
        			->select(['id', 'shop_name', 'phone', 'shop_review', 'shop_addr', 'shop_longitude', 'shop_latitude', 'user_name'])
        			->where(['shop_domain' => $domain_id])
        			->andWhere(['company_category_id' => $company_category_id])
        			->andWhere(['shop_status' => $join])
        			->andWhere(['like', 'shop_name', $shop_name])
        			->andWhere(['shop_type' => $type])
        			->orderBy('createtime desc')
                    ->asArray();
        } else {
            $shops = Shop::find()
            ->select(['id', 'shop_name', 'phone', 'shop_review', 'shop_addr', 'shop_longitude', 'shop_latitude', 'user_name'])
            ->where(['shop_domain' => $domain_id])
            ->andWhere(['company_category_id' => $company_category_id])
            ->andWhere(['<>', 'shop_status', 3])
            ->andWhere(['like', 'shop_name', $shop_name])
            ->andWhere(['shop_type' => $type])
            ->orderBy('createtime desc')
            ->asArray();
        }
    	
    	$pagination = new Pagination([
                'params'=>['page'=>$page],
               	'defaultPageSize' => $pageSize,
                'totalCount' => $shops->count(),
            ]);//分页参数
    	$result = $shops->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();
        if(!$result){
        	return false;
        }
        return $result;
    }   
    /**
     * 获取本地店铺列表集合（查云媒）
     */
    public function getBusinessList($shopName, $domain_id, $page, $type, $pageSize)
    {
        $shops = UserBusiness::find()
                ->select(['id', 'customer_name', 'customer_tel', 'customer_type', 'customer_longitude', 'customer_latitude'])
                ->where(['domain_id' => $domain_id])
                ->andWhere(['like', 'customer_name', $shopName])
                ->andWhere(['customer_type' => $type])
                ->orderBy('time desc')
                ->asArray();
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageSize,
            'totalCount' => $shops->count(),
        ]);//分页参数
        $result = $shops->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
        if(!$result){
            return false;
        }
        return $result;
    }
}